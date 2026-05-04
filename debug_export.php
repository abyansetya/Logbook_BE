<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Dokumen;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

try {
    echo "Starting export debug...\n";

    $query = Dokumen::with(['mitra.klasifikasiMitra', 'jenisDokumen', 'status', 'logs.user']);
    $dokumens = $query->orderBy('created_at', 'desc')->get();

    echo 'Fetched '.$dokumens->count()." documents.\n";

    // Group by Mitra
    $groupedData = $dokumens->groupBy(function ($item) {
        return $item->mitra ? $item->mitra->nama : 'Tanpa Mitra';
    });

    echo "Grouped data.\n";

    $spreadsheet = new Spreadsheet;
    $sheet = $spreadsheet->getActiveSheet();

    // Set Header
    $headers = ['No', 'Nama Mitra', 'Judul Dokumen', 'Tanggal', 'Keterangan', 'Contact Person', 'Nomor Dokumen', 'Status', 'Kriteria Mitra'];
    $columnLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

    foreach ($headers as $index => $header) {
        $sheet->setCellValue($columnLetters[$index].'1', $header);
    }

    echo "Headers set.\n";

    $row = 2;
    $no = 1;

    foreach ($groupedData as $mitraName => $docs) {
        // Calculate total rows for this Mitra
        $mitraStartRow = $row;

        foreach ($docs as $dokumen) {
            $dokumenStartRow = $row;
            $logs = $dokumen->logs->sortBy('tanggal_log');

            // If logs exist, iterate them. If not, one row for document.
            if ($logs->count() > 0) {
                foreach ($logs as $log) {
                    $sheet->setCellValue('D'.$row, $log->tanggal_log);
                    $sheet->setCellValue('E'.$row, $log->keterangan);
                    $sheet->setCellValue('F'.$row, $log->contact_person);
                    $row++;
                }
            } else {
                // Empty row for logs if none
                $row++;
            }

            $dokumenEndRow = $row - 1;

            // Merge Document Columns
            if ($dokumenEndRow > $dokumenStartRow) {
                $sheet->mergeCells("C{$dokumenStartRow}:C{$dokumenEndRow}"); // Judul
                $sheet->mergeCells("G{$dokumenStartRow}:G{$dokumenEndRow}"); // Nomor Dokumen
                $sheet->mergeCells("H{$dokumenStartRow}:H{$dokumenEndRow}"); // Status
            }

            $sheet->setCellValue('C'.$dokumenStartRow, $dokumen->judul_dokumen);

            $nomorDokumen = [];
            if ($dokumen->nomor_dokumen_undip) {
                $nomorDokumen[] = $dokumen->nomor_dokumen_undip;
            }
            if ($dokumen->nomor_dokumen_mitra) {
                $nomorDokumen[] = $dokumen->nomor_dokumen_mitra;
            }
            $sheet->setCellValue('G'.$dokumenStartRow, implode("\n", $nomorDokumen));
            $sheet->getStyle('G'.$dokumenStartRow)->getAlignment()->setWrapText(true);

            $sheet->setCellValue('H'.$dokumenStartRow, $dokumen->status ? $dokumen->status->nama : '-');
        }

        $mitraEndRow = $row - 1;

        // Merge Mitra Columns
        if ($mitraEndRow > $mitraStartRow) {
            $sheet->mergeCells("A{$mitraStartRow}:A{$mitraEndRow}"); // No
            $sheet->mergeCells("B{$mitraStartRow}:B{$mitraEndRow}"); // Nama Mitra
            $sheet->mergeCells("I{$mitraStartRow}:I{$mitraEndRow}"); // Kriteria Mitra
        }

        $sheet->setCellValue('A'.$mitraStartRow, $no++);
        $sheet->setCellValue('B'.$mitraStartRow, $mitraName);

        // Assuming Kriteria Mitra comes from KlasifikasiMitra relation of the first doc's mitra
        $kriteria = '';
        if ($docs->first()->mitra && $docs->first()->mitra->klasifikasiMitra) {
            $kriteria = $docs->first()->mitra->klasifikasiMitra->nama;
        }
        $sheet->setCellValue('I'.$mitraStartRow, $kriteria);
    }

    echo "Correction completed.\n";

} catch (\Exception $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    echo $e->getTraceAsString();
}
