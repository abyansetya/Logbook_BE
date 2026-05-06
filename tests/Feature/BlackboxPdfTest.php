<?php

namespace Tests\Feature;

use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\KlasifikasiMitra;
use App\Models\Log;
use App\Models\Mitra;
use App\Models\Role;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlackboxPdfTest extends TestCase
{
    use DatabaseTransactions;

    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminToken = $this->adminUser()->createToken('blackbox_test_token')->plainTextToken;
    }

    public function test_lb_1a_login_berhasil(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('message', 'Login berhasil')
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_lb_1b_login_gagal_password_salah(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password1234',
        ])->assertUnauthorized()
            ->assertJsonPath('message', 'Email atau password salah');
    }

    public function test_lb_1c_login_gagal_email_belum_terdaftar(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'adminn@example.com',
            'password' => 'password123',
        ])->assertUnauthorized()
            ->assertJsonPath('message', 'Email atau password salah');
    }

    public function test_lb_1d_login_gagal_format_email_invalid(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => '@admin.@example.com',
            'password' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_lb_1e_login_gagal_email_password_kosong(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => '',
            'password' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_lb_2a_logout_normal(): void
    {
        $token = $this->loginToken();

        $this->withToken($token)->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout berhasil');
    }

    public function test_lb_2b_mengakses_endpoint_setelah_logout_ditolak(): void
    {
        $token = $this->loginToken();

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();
        auth()->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/dashboard')->assertUnauthorized();
    }

    public function test_lb_3a_registrasi_berhasil_data_valid_pdf(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'nama' => 'jacob moe',
            'email' => 'jacobmoe.blackbox@example.com',
            'nim_nip' => '123454560',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated()
            ->assertJsonPath('message', 'Registrasi berhasil')
            ->assertJsonPath('data.user.role', 'Viewer');
    }

    public function test_lb_3b_registrasi_gagal_email_sudah_terdaftar(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'nama' => 'jacob moe',
            'email' => 'admin@example.com',
            'nim_nip' => '123454560',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_lb_3c_registrasi_gagal_email_tidak_valid(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'nama' => 'jacob moe',
            'email' => 'adminexample.com',
            'nim_nip' => '123454560',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_lb_3d_registrasi_gagal_password_kurang_dari_8_karakter(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'nama' => 'jacob moe',
            'email' => 'short.blackbox@example.com',
            'nim_nip' => '123454560',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_lb_3e_registrasi_gagal_field_wajib_kosong(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'nama' => '',
            'email' => '',
            'nim_nip' => '',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['nama', 'email', 'nim_nip', 'password']);
    }

    public function test_lb_3f_registrasi_gagal_konfirmasi_password_tidak_sama(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'nama' => 'jacob moe',
            'email' => 'confirm.blackbox@example.com',
            'nim_nip' => '123454560',
            'password' => 'Password123!',
            'password_confirmation' => 'Password1234!',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    public function test_lb_4a_melihat_dashboard_login(): void
    {
        $this->authGet('/api/v1/dashboard')->assertOk();
    }

    public function test_lb_5a_melihat_daftar_dokumen(): void
    {
        $this->authGet('/api/v1/logbook')->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_6a1_menambah_dokumen_data_valid(): void
    {
        Storage::fake('public');

        $this->authPost('/api/v1/logbook/dokumen', $this->dokumenPayload([
            'draft_dokumen' => UploadedFile::fake()->create('draft.pdf', 100, 'application/pdf'),
        ]))->assertCreated()
            ->assertJsonPath('success', true);
    }

    public function test_lb_6a2_menambah_dokumen_field_wajib_kosong(): void
    {
        $this->authPost('/api/v1/logbook/dokumen', $this->dokumenPayload([
            'judul_dokumen' => '',
            'mitra_id' => '',
            'jenis_dokumen_id' => '',
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['judul_dokumen', 'mitra_id', 'jenis_dokumen_id']);
    }

    public function test_lb_6a3_menambah_dokumen_draft_lebih_dari_2mb(): void
    {
        Storage::fake('public');

        $this->authPost('/api/v1/logbook/dokumen', $this->dokumenPayload([
            'draft_dokumen' => UploadedFile::fake()->create('draft-large.pdf', 3000, 'application/pdf'),
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['draft_dokumen']);
    }

    public function test_lb_6a4_menambah_dokumen_nomor_sudah_terdaftar(): void
    {
        $this->createDokumen(['nomor_dokumen_mitra' => 'UGM/MOU/2026', 'nomor_dokumen_undip' => 'UN7/MOU/2026']);

        $this->authPost('/api/v1/logbook/dokumen', $this->dokumenPayload([
            'nomor_dokumen_mitra' => 'UGM/MOU/2026',
            'nomor_dokumen_undip' => 'UN7/MOU/2026',
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['nomor_dokumen_mitra', 'nomor_dokumen_undip']);
    }

    public function test_lb_6b1_mengubah_dokumen_data_valid(): void
    {
        Storage::fake('public');

        $dokumen = $this->createDokumen();
        $this->authPut("/api/v1/logbook/edit-dokumen/{$dokumen->id}", $this->dokumenPayload([
            'status_id' => $this->statusTerbit()->id,
            'final_dokumen' => UploadedFile::fake()->create('final.pdf', 100, 'application/pdf'),
            'tanggal_terbit' => '2026-12-12',
        ]))->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_6b2_mengubah_dokumen_field_wajib_kosong(): void
    {
        $dokumen = $this->createDokumen();

        $this->authPut("/api/v1/logbook/edit-dokumen/{$dokumen->id}", $this->dokumenPayload([
            'judul_dokumen' => '',
            'mitra_id' => '',
            'jenis_dokumen_id' => '',
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['judul_dokumen', 'mitra_id', 'jenis_dokumen_id']);
    }

    public function test_lb_6b3_mengubah_dokumen_final_lebih_dari_2mb(): void
    {
        Storage::fake('public');

        $dokumen = $this->createDokumen();
        $this->authPut("/api/v1/logbook/edit-dokumen/{$dokumen->id}", $this->dokumenPayload([
            'status_id' => $this->statusTerbit()->id,
            'final_dokumen' => UploadedFile::fake()->create('final-large.pdf', 3000, 'application/pdf'),
            'tanggal_terbit' => '2026-12-12',
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['final_dokumen']);
    }

    public function test_lb_6b4_mengubah_dokumen_nomor_sudah_terdaftar(): void
    {
        $existing = $this->createDokumen(['nomor_dokumen_mitra' => 'UGM/MOU/2026', 'nomor_dokumen_undip' => 'UN7/MOU/2026']);
        $target = $this->createDokumen(['nomor_dokumen_mitra' => 'OTHER/MOU/2026', 'nomor_dokumen_undip' => 'UN7/OTHER/2026']);

        $this->authPut("/api/v1/logbook/edit-dokumen/{$target->id}", $this->dokumenPayload([
            'nomor_dokumen_mitra' => $existing->nomor_dokumen_mitra,
            'nomor_dokumen_undip' => $existing->nomor_dokumen_undip,
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors(['nomor_dokumen_mitra', 'nomor_dokumen_undip']);
    }

    public function test_lb_6c1_menghapus_data_dokumen(): void
    {
        $dokumen = $this->createDokumen();

        $this->authDelete("/api/v1/logbook/delete-dokumen/{$dokumen->id}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_7a_melihat_daftar_log_dokumen(): void
    {
        $dokumen = $this->createDokumen();
        $this->createLog($dokumen);

        $this->authGet("/api/v1/logbook/dokumen/{$dokumen->id}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_8a1_menambah_log_data_valid(): void
    {
        $dokumen = $this->createDokumen();

        $this->authPost('/api/v1/logbook/add-log', $this->logPayload($dokumen))
            ->assertCreated()
            ->assertJsonPath('success', true);
    }

    public function test_lb_8a2_menambah_log_tanggal_kosong(): void
    {
        $dokumen = $this->createDokumen();

        $this->authPost('/api/v1/logbook/add-log', $this->logPayload($dokumen, ['tanggal_log' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['tanggal_log']);
    }

    public function test_lb_8a3_menambah_log_unit_kosong(): void
    {
        $dokumen = $this->createDokumen();

        $this->authPost('/api/v1/logbook/add-log', $this->logPayload($dokumen, ['unit_id' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['unit_id']);
    }

    public function test_lb_8a4_menambah_log_keterangan_kosong(): void
    {
        $dokumen = $this->createDokumen();

        $this->authPost('/api/v1/logbook/add-log', $this->logPayload($dokumen, ['keterangan' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['keterangan']);
    }

    public function test_lb_8b1_mengedit_log_data_valid(): void
    {
        $dokumen = $this->createDokumen();
        $log = $this->createLog($dokumen);

        $this->authPut("/api/v1/logbook/edit-log/{$log->id}", [
            'user_id' => $this->adminUser()->id,
            'unit_id' => $this->unitDho()->id,
            'tanggal_log' => '2026-12-12',
            'keterangan' => 'Menunggu tanda tangan dekan FSM',
        ])->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_8b2_mengedit_log_tanggal_kosong(): void
    {
        $dokumen = $this->createDokumen();
        $log = $this->createLog($dokumen);

        $this->authPut("/api/v1/logbook/edit-log/{$log->id}", [
            'user_id' => $this->adminUser()->id,
            'unit_id' => $this->unitDho()->id,
            'tanggal_log' => '',
            'keterangan' => 'Menunggu persetujuan DHO',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['tanggal_log']);
    }

    public function test_lb_8b3_mengedit_log_keterangan_kosong(): void
    {
        $dokumen = $this->createDokumen();
        $log = $this->createLog($dokumen);

        $this->authPut("/api/v1/logbook/edit-log/{$log->id}", [
            'user_id' => $this->adminUser()->id,
            'unit_id' => $this->unitDho()->id,
            'tanggal_log' => '2026-12-12',
            'keterangan' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['keterangan']);
    }

    public function test_lb_8c1_menghapus_log(): void
    {
        $dokumen = $this->createDokumen();
        $log = $this->createLog($dokumen);

        $this->authDelete("/api/v1/logbook/delete-log/{$log->id}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_9a_melihat_daftar_mitra(): void
    {
        $this->authGet('/api/v1/mitra')->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_10a1_menambahkan_mitra_data_valid(): void
    {
        $this->authPost('/api/v1/mitra', $this->mitraPayload())
            ->assertCreated()
            ->assertJsonPath('success', true);
    }

    public function test_lb_10a2_menambahkan_mitra_nama_kosong(): void
    {
        $this->authPost('/api/v1/mitra', $this->mitraPayload(['nama' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama']);
    }

    public function test_lb_10b1_mengedit_mitra_data_valid(): void
    {
        $mitra = Mitra::create($this->mitraPayload(['nama' => 'Blackbox Mitra Edit Source']));

        $this->authPut("/api/v1/mitra/{$mitra->id}", $this->mitraPayload(['nama' => 'Universitas Pasundan']))
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_10b2_mengedit_mitra_nama_kosong(): void
    {
        $mitra = Mitra::create($this->mitraPayload(['nama' => 'Blackbox Mitra Invalid Edit']));

        $this->authPut("/api/v1/mitra/{$mitra->id}", $this->mitraPayload(['nama' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama']);
    }

    public function test_lb_10c1_menghapus_data_mitra(): void
    {
        $mitra = Mitra::create($this->mitraPayload(['nama' => 'Blackbox Mitra Delete']));

        $this->authDelete("/api/v1/mitra/{$mitra->id}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_11a_melihat_daftar_user(): void
    {
        $this->authGet('/api/v1/users')->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_lb_12a1_mengganti_peran_pengguna(): void
    {
        $user = $this->viewerUser('role-change.blackbox@example.com');

        $this->authPut("/api/v1/users/{$user->id}/role", ['role' => 'Admin'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.role', 'Admin');
    }

    public function test_lb_12b1_menghapus_data_user(): void
    {
        $user = $this->viewerUser('delete.blackbox@example.com');

        $this->authDelete("/api/v1/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('message', 'User berhasil dihapus');

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    public function test_lb_13a_melihat_daftar_unit(): void
    {
        $this->authGet('/api/v1/unit')->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_14a1_menambahkan_unit_data_valid(): void
    {
        $this->authPost('/api/v1/unit', ['nama' => 'DHO Blackbox'])
            ->assertCreated()
            ->assertJsonPath('success', true);
    }

    public function test_lb_14a2_menambahkan_unit_nama_kosong(): void
    {
        $this->authPost('/api/v1/unit', ['nama' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama']);
    }

    public function test_lb_14b1_mengubah_unit_data_valid(): void
    {
        $unit = Unit::create(['nama' => 'Blackbox Unit']);

        $this->authPut("/api/v1/unit/{$unit->id}", ['nama' => 'Rektor Blackbox'])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_14b2_mengubah_unit_nama_kosong(): void
    {
        $unit = Unit::create(['nama' => 'Blackbox Unit Invalid']);

        $this->authPut("/api/v1/unit/{$unit->id}", ['nama' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama']);
    }

    public function test_lb_14b3_menghapus_data_unit(): void
    {
        $unit = Unit::create(['nama' => 'Blackbox Unit Delete']);

        $this->authDelete("/api/v1/unit/{$unit->id}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_15a_melihat_daftar_status(): void
    {
        $this->authGet('/api/v1/status')->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_16a1_menambahkan_status_data_valid(): void
    {
        $this->authPost('/api/v1/status', ['nama' => 'Ditolak Blackbox'])
            ->assertCreated()
            ->assertJsonPath('success', true);
    }

    public function test_lb_16a2_menambahkan_status_nama_kosong(): void
    {
        $this->authPost('/api/v1/status', ['nama' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama']);
    }

    public function test_lb_16b1_mengubah_status_data_valid(): void
    {
        $status = Status::create(['nama' => 'Blackbox Status']);

        $this->authPut("/api/v1/status/{$status->id}", ['nama' => 'Pending Blackbox'])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_16b2_mengubah_status_nama_kosong(): void
    {
        $status = Status::create(['nama' => 'Blackbox Status Invalid']);

        $this->authPut("/api/v1/status/{$status->id}", ['nama' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama']);
    }

    public function test_lb_16c1_menghapus_data_status(): void
    {
        $status = Status::create(['nama' => 'Blackbox Status Delete']);

        $this->authDelete("/api/v1/status/{$status->id}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_lb_17a_download_data_dokumen_dan_log(): void
    {
        $response = $this->withToken($this->adminToken)->get('/api/v1/logbook/export');

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
    }

    private function authGet(string $uri)
    {
        return $this->withToken($this->adminToken)->getJson($uri);
    }

    private function authPost(string $uri, array $data)
    {
        return $this->withToken($this->adminToken)->postJson($uri, $data);
    }

    private function authPut(string $uri, array $data)
    {
        return $this->withToken($this->adminToken)->putJson($uri, $data);
    }

    private function authDelete(string $uri)
    {
        return $this->withToken($this->adminToken)->deleteJson($uri);
    }

    private function loginToken(): string
    {
        return $this->adminUser()->createToken('blackbox_test_token')->plainTextToken;
    }

    private function dokumenPayload(array $overrides = []): array
    {
        return array_merge([
            'judul_dokumen' => 'Memorandum of Understanding antara Undip dan UGM',
            'mitra_id' => $this->mitraUgm()->id,
            'jenis_dokumen_id' => $this->jenisMou()->id,
            'status_id' => $this->statusProses()->id,
            'contact_person' => 'hany/081292929',
            'nomor_dokumen_mitra' => 'UGM/MOU/2026-BB-'.uniqid(),
            'nomor_dokumen_undip' => 'UN7/MOU/2026-BB-'.uniqid(),
            'tanggal_masuk' => '2026-12-10',
            'tanggal_terbit' => null,
        ], $overrides);
    }

    private function logPayload(Dokumen $dokumen, array $overrides = []): array
    {
        return array_merge([
            'mitra_id' => $dokumen->mitra_id,
            'dokumen_id' => $dokumen->id,
            'unit_id' => $this->unitDho()->id,
            'tanggal_log' => '2026-12-12',
            'keterangan' => 'Menunggu persetujuan DHO',
        ], $overrides);
    }

    private function mitraPayload(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Universitas Indonesia Blackbox '.uniqid(),
            'klasifikasi_mitra_id' => KlasifikasiMitra::query()->where('nama', 'like', '%Institusi Pendidikan%')->firstOrFail()->id,
            'alamat' => 'Depok',
            'contact_person' => '0812933383',
        ], $overrides);
    }

    private function createDokumen(array $overrides = []): Dokumen
    {
        return Dokumen::create($this->dokumenPayload($overrides));
    }

    private function createLog(Dokumen $dokumen): Log
    {
        return Log::create([
            'user_id' => $this->adminUser()->id,
            'mitra_id' => $dokumen->mitra_id,
            'dokumen_id' => $dokumen->id,
            'unit_id' => $this->unitDho()->id,
            'tanggal_log' => '2026-12-12',
            'keterangan' => 'Menunggu persetujuan DHO',
        ]);
    }

    private function viewerUser(string $email): User
    {
        return User::create([
            'nama' => 'Blackbox Viewer',
            'email' => $email,
            'nim_nip' => uniqid('BB'),
            'password' => Hash::make('Password123!'),
            'role_id' => Role::where('nama', 'Viewer')->firstOrFail()->id,
        ]);
    }

    private function adminUser(): User
    {
        return User::where('email', 'admin@example.com')->firstOrFail();
    }

    private function mitraUgm(): Mitra
    {
        return Mitra::where('nama', 'like', '%Gadjah Mada%')->firstOrFail();
    }

    private function jenisMou(): JenisDokumen
    {
        return JenisDokumen::where('nama', 'like', '%Understanding%')->firstOrFail();
    }

    private function statusProses(): Status
    {
        return Status::where('nama', '!=', 'Terbit')->firstOrFail();
    }

    private function statusTerbit(): Status
    {
        return Status::where('nama', 'Terbit')->firstOrFail();
    }

    private function unitDho(): Unit
    {
        return Unit::where('nama', 'DHO')->firstOrFail();
    }
}
