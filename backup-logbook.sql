--
-- PostgreSQL database dump
--

\restrict iiO5i8m1btl5DJdNisc6yfw1ACO9Ko5pvS3tpT9G7QJa9YM36k7kyksnsxUBIh4

-- Dumped from database version 16.14 (Debian 16.14-1.pgdg13+1)
-- Dumped by pg_dump version 16.14 (Debian 16.14-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: activity_logs; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.activity_logs (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    action character varying(100) NOT NULL,
    description text,
    type character varying(50) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.activity_logs OWNER TO logbook_user;

--
-- Name: activity_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.activity_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.activity_logs_id_seq OWNER TO logbook_user;

--
-- Name: activity_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.activity_logs_id_seq OWNED BY public.activity_logs.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO logbook_user;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO logbook_user;

--
-- Name: dokumen; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.dokumen (
    id bigint NOT NULL,
    mitra_id bigint NOT NULL,
    jenis_dokumen_id bigint NOT NULL,
    nomor_dokumen_mitra character varying(50),
    nomor_dokumen_undip character varying(50),
    judul_dokumen character varying(200) NOT NULL,
    status_id bigint NOT NULL,
    tanggal_masuk date,
    tanggal_terbit date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    contact_person character varying(255),
    tanggal_dokumen date,
    draft_dokumen character varying(255),
    final_dokumen character varying(255),
    user_id bigint
);


ALTER TABLE public.dokumen OWNER TO logbook_user;

--
-- Name: dokumen_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.dokumen_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.dokumen_id_seq OWNER TO logbook_user;

--
-- Name: dokumen_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.dokumen_id_seq OWNED BY public.dokumen.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO logbook_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO logbook_user;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: jenis_dokumen; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.jenis_dokumen (
    id bigint NOT NULL,
    nama character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.jenis_dokumen OWNER TO logbook_user;

--
-- Name: jenis_dokumen_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.jenis_dokumen_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jenis_dokumen_id_seq OWNER TO logbook_user;

--
-- Name: jenis_dokumen_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.jenis_dokumen_id_seq OWNED BY public.jenis_dokumen.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO logbook_user;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO logbook_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO logbook_user;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: klasifikasi_mitra; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.klasifikasi_mitra (
    id bigint NOT NULL,
    nama character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.klasifikasi_mitra OWNER TO logbook_user;

--
-- Name: klasifikasi_mitra_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.klasifikasi_mitra_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.klasifikasi_mitra_id_seq OWNER TO logbook_user;

--
-- Name: klasifikasi_mitra_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.klasifikasi_mitra_id_seq OWNED BY public.klasifikasi_mitra.id;


--
-- Name: log; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.log (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    mitra_id bigint NOT NULL,
    dokumen_id bigint NOT NULL,
    keterangan text,
    tanggal_log date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    unit_id bigint
);


ALTER TABLE public.log OWNER TO logbook_user;

--
-- Name: log_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.log_id_seq OWNER TO logbook_user;

--
-- Name: log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.log_id_seq OWNED BY public.log.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO logbook_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO logbook_user;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: mitra; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.mitra (
    id bigint NOT NULL,
    nama character varying(150) NOT NULL,
    klasifikasi_mitra_id bigint NOT NULL,
    alamat text,
    contact_person character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status character varying(50),
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.mitra OWNER TO logbook_user;

--
-- Name: mitra_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.mitra_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mitra_id_seq OWNER TO logbook_user;

--
-- Name: mitra_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.mitra_id_seq OWNED BY public.mitra.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.password_reset_tokens (
    email character varying(150) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO logbook_user;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO logbook_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO logbook_user;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    nama character varying(50) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO logbook_user;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO logbook_user;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO logbook_user;

--
-- Name: status; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.status (
    id bigint NOT NULL,
    nama character varying(50) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.status OWNER TO logbook_user;

--
-- Name: status_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.status_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.status_id_seq OWNER TO logbook_user;

--
-- Name: status_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.status_id_seq OWNED BY public.status.id;


--
-- Name: unit; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.unit (
    id bigint NOT NULL,
    nama character varying(100) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.unit OWNER TO logbook_user;

--
-- Name: unit_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.unit_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.unit_id_seq OWNER TO logbook_user;

--
-- Name: unit_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.unit_id_seq OWNED BY public.unit.id;


--
-- Name: user_roles; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.user_roles (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    role_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_roles OWNER TO logbook_user;

--
-- Name: user_roles_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.user_roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_roles_id_seq OWNER TO logbook_user;

--
-- Name: user_roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.user_roles_id_seq OWNED BY public.user_roles.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: logbook_user
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    nama character varying(100) NOT NULL,
    email character varying(150) NOT NULL,
    password character varying(255) NOT NULL,
    nim_nip character varying(25) NOT NULL,
    role_id bigint,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    account_status character varying(255) DEFAULT 'approved'::character varying NOT NULL
);


ALTER TABLE public.users OWNER TO logbook_user;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: logbook_user
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO logbook_user;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: logbook_user
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: activity_logs id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.activity_logs ALTER COLUMN id SET DEFAULT nextval('public.activity_logs_id_seq'::regclass);


--
-- Name: dokumen id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.dokumen ALTER COLUMN id SET DEFAULT nextval('public.dokumen_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jenis_dokumen id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.jenis_dokumen ALTER COLUMN id SET DEFAULT nextval('public.jenis_dokumen_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: klasifikasi_mitra id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.klasifikasi_mitra ALTER COLUMN id SET DEFAULT nextval('public.klasifikasi_mitra_id_seq'::regclass);


--
-- Name: log id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.log ALTER COLUMN id SET DEFAULT nextval('public.log_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: mitra id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.mitra ALTER COLUMN id SET DEFAULT nextval('public.mitra_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: status id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.status ALTER COLUMN id SET DEFAULT nextval('public.status_id_seq'::regclass);


--
-- Name: unit id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.unit ALTER COLUMN id SET DEFAULT nextval('public.unit_id_seq'::regclass);


--
-- Name: user_roles id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.user_roles ALTER COLUMN id SET DEFAULT nextval('public.user_roles_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: activity_logs; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.activity_logs (id, user_id, action, description, type, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: dokumen; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.dokumen (id, mitra_id, jenis_dokumen_id, nomor_dokumen_mitra, nomor_dokumen_undip, judul_dokumen, status_id, tanggal_masuk, tanggal_terbit, created_at, updated_at, contact_person, tanggal_dokumen, draft_dokumen, final_dokumen, user_id) FROM stdin;
1	1	1	PT /MOU/846/2025	UNDIP/MOU/992/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Gojek Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-09-14	2025-10-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Mursinin Purwa Ardianto - (+62) 919 5453 7902	2025-09-14	\N	\N	\N
2	1	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Gojek Indonesia tentang Program Magang Mahasiswa	3	2026-06-14	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Rina Halimah - (+62) 786 4547 973	2026-06-14	\N	\N	\N
3	1	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Gojek Indonesia	1	2026-06-02	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Septi Hassanah - (+62) 371 5557 856	2026-06-02	\N	\N	\N
4	2	1	PT /MOU/255/2025	UNDIP/MOU/118/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Telekomunikasi Indonesia (Persero) Tbk tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-08-14	2025-09-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Bahuwirya Natsir S.IP - (+62) 757 6870 4745	2025-08-14	\N	\N	\N
5	2	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Telekomunikasi Indonesia (Persero) Tbk	1	2026-05-26	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Bajragin Maulana - (+62) 323 3863 887	2026-05-26	\N	\N	\N
6	3	1	PT /MOU/939/2026	UNDIP/MOU/905/2026	Nota Kesepahaman antara Universitas Diponegoro dan PT Pertamina (Persero) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2026-01-14	2026-02-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Humaira Rahmawati S.E.I - 0481 2343 3679	2026-01-14	\N	\N	\N
7	3	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Pertamina (Persero) tentang Program Magang Mahasiswa	1	2026-06-14	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Carla Rahmawati S.Gz - 0987 9436 915	2026-06-14	\N	\N	\N
8	3	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Pertamina (Persero)	1	2026-06-09	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Irwan Kurniawan S.Ked - 0300 7496 4057	2026-06-09	\N	\N	\N
9	4	1	GOO/MOU/124/2026	UNDIP/MOU/316/2026	Nota Kesepahaman antara Universitas Diponegoro dan Google Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2026-01-14	2026-02-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Vera Najwa Mandasari - (+62) 315 4438 0007	2026-01-14	\N	\N	\N
10	5	1	MIC/MOU/877/2025	UNDIP/MOU/366/2025	Nota Kesepahaman antara Universitas Diponegoro dan Microsoft Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-10-14	2025-11-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Lurhur Nainggolan - 0587 3324 202	2025-10-14	\N	\N	\N
11	6	1	UNI/MOU/821/2025	UNDIP/MOU/650/2025	Nota Kesepahaman antara Universitas Diponegoro dan Universitas Indonesia (UI) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-11-14	2025-12-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Patricia Wahyuni - (+62) 25 3376 0140	2025-11-14	\N	\N	\N
12	6	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari Universitas Indonesia (UI)	1	2026-06-30	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Kajen Gandi Pradana - 0993 9922 893	2026-06-30	\N	\N	\N
13	7	1	INS/MOU/808/2025	UNDIP/MOU/192/2025	Nota Kesepahaman antara Universitas Diponegoro dan Institut Teknologi Bandung (ITB) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-10-14	2025-11-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Warsa Radit Pranowo - (+62) 898 496 019	2025-10-14	\N	\N	\N
14	8	1	UNI/MOU/558/2025	UNDIP/MOU/636/2025	Nota Kesepahaman antara Universitas Diponegoro dan Universitas Gadjah Mada (UGM) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-11-14	2025-12-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Jelita Ami Palastri S.I.Kom - 0829 2040 7017	2025-11-14	\N	\N	\N
15	8	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan Universitas Gadjah Mada (UGM) tentang Program Magang Mahasiswa	4	2026-03-14	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Purwanto Okto Iswahyudi - 023 5621 7248	2026-03-14	\N	\N	\N
16	8	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari Universitas Gadjah Mada (UGM)	1	2026-06-23	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Opan Mangunsong - 0697 7655 4891	2026-06-23	\N	\N	\N
17	9	1	PT /MOU/236/2025	UNDIP/MOU/702/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Bank Central Asia Tbk (BCA) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-08-14	2025-09-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Victoria Laksita - 0897 743 162	2025-08-14	\N	\N	\N
18	10	1	PT /MOU/606/2025	UNDIP/MOU/138/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Bank Rakyat Indonesia (Persero) Tbk tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-08-14	2025-09-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Pranawa Prabowo - (+62) 496 5437 5260	2025-08-14	\N	\N	\N
19	10	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Bank Rakyat Indonesia (Persero) Tbk tentang Program Magang Mahasiswa	3	2026-06-14	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Wulan Syahrini Suartini M.Farm - 0790 6220 1382	2026-06-14	\N	\N	\N
20	11	1	PT /MOU/553/2025	UNDIP/MOU/111/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Shopee International Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-11-14	2025-12-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Gatot Wacana S.Gz - 0649 2892 7796	2025-11-14	\N	\N	\N
21	11	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Shopee International Indonesia tentang Program Magang Mahasiswa	1	2026-03-14	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Cagak Najam Hardiansyah - 0925 0794 4841	2026-03-14	\N	\N	\N
22	11	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Shopee International Indonesia	1	2026-06-09	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Puput Almira Nasyidah S.Gz - (+62) 822 9971 6013	2026-06-09	\N	\N	\N
23	12	1	PT /MOU/497/2025	UNDIP/MOU/422/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Tokopedia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-11-14	2025-12-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Gadang Santoso - 0943 8991 7928	2025-11-14	\N	\N	\N
24	12	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Tokopedia tentang Program Magang Mahasiswa	2	2026-04-14	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Ratna Cici Safitri - (+62) 958 5571 3128	2026-04-14	\N	\N	\N
25	12	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Tokopedia	1	2026-06-30	\N	2026-07-14 18:36:53	2026-07-14 18:36:53	Simon Gunawan M.Kom. - (+62) 631 5864 064	2026-06-30	\N	\N	\N
26	13	1	BAD/MOU/485/2025	UNDIP/MOU/410/2025	Nota Kesepahaman antara Universitas Diponegoro dan Badan Riset dan Inovasi Nasional (BRIN) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-10-14	2025-11-14	2026-07-14 18:36:53	2026-07-14 18:36:53	Cici Rahayu - 0363 1105 348	2025-10-14	\N	\N	\N
27	1	1	PT /MOU/707/2026	UNDIP/MOU/294/2026	Nota Kesepahaman antara Universitas Diponegoro dan PT Gojek Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-12-15	2026-01-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Gaduh Balangga Suwarno - (+62) 727 6224 821	2025-12-15	\N	\N	\N
28	2	1	PT /MOU/761/2025	UNDIP/MOU/430/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Telekomunikasi Indonesia (Persero) Tbk tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-09-15	2025-10-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Patricia Wahyuni - 0380 7493 4200	2025-09-15	\N	\N	\N
29	2	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Telekomunikasi Indonesia (Persero) Tbk tentang Program Magang Mahasiswa	4	2026-04-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Laswi Hutasoit - (+62) 647 8650 120	2026-04-15	\N	\N	\N
30	2	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Telekomunikasi Indonesia (Persero) Tbk	1	2026-05-27	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Harja Santoso M.Pd - 0220 8896 3667	2026-05-27	\N	\N	\N
31	3	1	PT /MOU/100/2025	UNDIP/MOU/330/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Pertamina (Persero) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-09-15	2025-10-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Kania Puspasari S.I.Kom - 0710 8595 4332	2025-09-15	\N	\N	\N
32	4	1	GOO/MOU/849/2025	UNDIP/MOU/161/2025	Nota Kesepahaman antara Universitas Diponegoro dan Google Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-08-15	2025-09-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Lintang Puspa Pudjiastuti S.Pd - 020 6921 382	2025-08-15	\N	\N	\N
33	4	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan Google Indonesia tentang Program Magang Mahasiswa	2	2026-02-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Ivan Gunarto - (+62) 807 1171 045	2026-02-15	\N	\N	\N
34	4	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari Google Indonesia	1	2026-07-01	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Uchita Alika Mardhiyah S.Gz - (+62) 320 3736 472	2026-07-01	\N	\N	\N
35	5	1	MIC/MOU/646/2025	UNDIP/MOU/230/2025	Nota Kesepahaman antara Universitas Diponegoro dan Microsoft Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-07-15	2025-08-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Patricia Haryanti S.Pt - (+62) 379 2902 3664	2025-07-15	\N	\N	\N
36	5	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan Microsoft Indonesia tentang Program Magang Mahasiswa	4	2026-04-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Samsul Maryadi - 0641 0220 2139	2026-04-15	\N	\N	\N
37	6	1	UNI/MOU/711/2025	UNDIP/MOU/614/2025	Nota Kesepahaman antara Universitas Diponegoro dan Universitas Indonesia (UI) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-09-15	2025-10-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Mila Siska Wastuti S.Pt - 0971 5793 9719	2025-09-15	\N	\N	\N
38	6	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan Universitas Indonesia (UI) tentang Program Magang Mahasiswa	3	2026-02-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Karsana Damanik - 027 2401 883	2026-02-15	\N	\N	\N
39	6	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari Universitas Indonesia (UI)	1	2026-06-03	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Indra Ihsan Gunawan - 0723 5975 9669	2026-06-03	\N	\N	\N
40	7	1	INS/MOU/901/2025	UNDIP/MOU/345/2025	Nota Kesepahaman antara Universitas Diponegoro dan Institut Teknologi Bandung (ITB) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-07-15	2025-08-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Belinda Lailasari - (+62) 410 3162 0803	2025-07-15	\N	\N	\N
41	7	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan Institut Teknologi Bandung (ITB) tentang Program Magang Mahasiswa	4	2026-03-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Zaenab Handayani - (+62) 399 9976 122	2026-03-15	\N	\N	\N
42	8	1	UNI/MOU/480/2025	UNDIP/MOU/356/2025	Nota Kesepahaman antara Universitas Diponegoro dan Universitas Gadjah Mada (UGM) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-10-15	2025-11-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Garan Emas Gunarto S.T. - 0640 7502 9643	2025-10-15	\N	\N	\N
43	9	1	PT /MOU/624/2025	UNDIP/MOU/133/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Bank Central Asia Tbk (BCA) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-07-15	2025-08-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Gatra Endra Sinaga - (+62) 28 4065 241	2025-07-15	\N	\N	\N
44	9	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Bank Central Asia Tbk (BCA)	1	2026-05-27	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Caraka Naradi Jailani - 0328 0798 482	2026-05-27	\N	\N	\N
45	10	1	PT /MOU/430/2025	UNDIP/MOU/119/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Bank Rakyat Indonesia (Persero) Tbk tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-07-15	2025-08-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Pandu Megantara - (+62) 690 9451 9224	2025-07-15	\N	\N	\N
46	10	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Bank Rakyat Indonesia (Persero) Tbk tentang Program Magang Mahasiswa	2	2026-03-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Jati Gunawan - 0802 9926 8489	2026-03-15	\N	\N	\N
47	10	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Bank Rakyat Indonesia (Persero) Tbk	1	2026-06-03	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Tira Rahayu - (+62) 389 0179 058	2026-06-03	\N	\N	\N
48	11	1	PT /MOU/878/2025	UNDIP/MOU/716/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Shopee International Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-08-15	2025-09-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Gawati Zulaika - 0846 0872 0097	2025-08-15	\N	\N	\N
49	12	1	PT /MOU/194/2025	UNDIP/MOU/320/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Tokopedia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-08-15	2025-09-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Ina Sudiati - 0788 1281 7388	2025-08-15	\N	\N	\N
50	13	1	BAD/MOU/168/2026	UNDIP/MOU/884/2026	Nota Kesepahaman antara Universitas Diponegoro dan Badan Riset dan Inovasi Nasional (BRIN) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2026-01-15	2026-02-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Xanana Saragih M.Kom. - 0813 348 864	2026-01-15	\N	\N	\N
51	13	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan Badan Riset dan Inovasi Nasional (BRIN) tentang Program Magang Mahasiswa	3	2026-05-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Anita Belinda Prastuti - (+62) 22 2581 069	2026-05-15	\N	\N	\N
52	14	1	PT /MOU/690/2025	UNDIP/MOU/449/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Gojek Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-07-15	2025-08-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Usyi Nuraini - 0456 9151 495	2025-07-15	\N	\N	\N
53	14	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Gojek Indonesia	1	2026-06-17	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Michelle Wulandari - 0629 3897 154	2026-06-17	\N	\N	\N
54	15	1	PT /MOU/738/2025	UNDIP/MOU/809/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Telekomunikasi Indonesia (Persero) Tbk tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-08-15	2025-09-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Karen Lintang Mayasari S.Pd - 0927 6140 4124	2025-08-15	\N	\N	\N
55	15	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Telekomunikasi Indonesia (Persero) Tbk tentang Program Magang Mahasiswa	4	2026-05-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Dimaz Dabukke - 0877 785 140	2026-05-15	\N	\N	\N
56	16	1	PT /MOU/825/2026	UNDIP/MOU/579/2026	Nota Kesepahaman antara Universitas Diponegoro dan PT Pertamina (Persero) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2026-01-15	2026-02-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Kania Kamaria Pertiwi S.T. - (+62) 509 2027 902	2026-01-15	\N	\N	\N
57	16	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Pertamina (Persero) tentang Program Magang Mahasiswa	2	2026-04-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Enteng Prabowo S.H. - 0586 2052 6505	2026-04-15	\N	\N	\N
58	17	1	GOO/MOU/531/2025	UNDIP/MOU/994/2025	Nota Kesepahaman antara Universitas Diponegoro dan Google Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-09-15	2025-10-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Asman Soleh Manullang S.E. - 0800 1646 864	2025-09-15	\N	\N	\N
59	17	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari Google Indonesia	1	2026-06-03	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Ihsan Gambira Haryanto - (+62) 744 6323 4328	2026-06-03	\N	\N	\N
60	18	1	MIC/MOU/939/2025	UNDIP/MOU/892/2025	Nota Kesepahaman antara Universitas Diponegoro dan Microsoft Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-09-15	2025-10-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Titi Kusmawati - 0677 9586 464	2025-09-15	\N	\N	\N
61	19	1	UNI/MOU/371/2026	UNDIP/MOU/651/2026	Nota Kesepahaman antara Universitas Diponegoro dan Universitas Indonesia (UI) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-12-15	2026-01-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Raisa Usada S.Farm - (+62) 290 9668 083	2025-12-15	\N	\N	\N
62	20	1	INS/MOU/985/2025	UNDIP/MOU/242/2025	Nota Kesepahaman antara Universitas Diponegoro dan Institut Teknologi Bandung (ITB) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-11-15	2025-12-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Jelita Padmasari - (+62) 642 8963 5658	2025-11-15	\N	\N	\N
63	20	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan Institut Teknologi Bandung (ITB) tentang Program Magang Mahasiswa	2	2026-06-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Calista Farida - (+62) 925 7250 5145	2026-06-15	\N	\N	\N
64	20	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari Institut Teknologi Bandung (ITB)	1	2026-06-24	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Unggul Tampubolon M.Kom. - 0524 7319 096	2026-06-24	\N	\N	\N
65	21	1	UNI/MOU/212/2025	UNDIP/MOU/773/2025	Nota Kesepahaman antara Universitas Diponegoro dan Universitas Gadjah Mada (UGM) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-09-15	2025-10-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Jayadi Anggriawan - 0413 5004 471	2025-09-15	\N	\N	\N
66	21	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari Universitas Gadjah Mada (UGM)	1	2026-06-03	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Hasim Johan Firgantoro - (+62) 896 867 682	2026-06-03	\N	\N	\N
67	22	1	PT /MOU/842/2025	UNDIP/MOU/487/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Bank Central Asia Tbk (BCA) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-10-15	2025-11-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Bagas Waluyo M.Farm - 0774 4847 212	2025-10-15	\N	\N	\N
68	23	1	PT /MOU/402/2025	UNDIP/MOU/697/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Bank Rakyat Indonesia (Persero) Tbk tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-11-15	2025-12-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Hasna Novitasari M.Ak - 0210 4308 144	2025-11-15	\N	\N	\N
69	24	1	PT /MOU/254/2026	UNDIP/MOU/849/2026	Nota Kesepahaman antara Universitas Diponegoro dan PT Shopee International Indonesia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-12-15	2026-01-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Ajiman Budiman - 0935 0240 139	2025-12-15	\N	\N	\N
70	24	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Shopee International Indonesia	1	2026-07-08	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Rafi Xanana Habibi - 0726 4803 2711	2026-07-08	\N	\N	\N
71	25	1	PT /MOU/306/2025	UNDIP/MOU/798/2025	Nota Kesepahaman antara Universitas Diponegoro dan PT Tokopedia tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-07-15	2025-08-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Banara Manullang - 0861 8983 5976	2025-07-15	\N	\N	\N
72	25	2	\N	\N	Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan PT Tokopedia tentang Program Magang Mahasiswa	1	2026-02-15	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Legawa Budiyanto - (+62) 296 7067 310	2026-02-15	\N	\N	\N
73	25	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari PT Tokopedia	1	2026-06-03	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Cayadi Situmorang - 0564 8865 867	2026-06-03	\N	\N	\N
74	26	1	BAD/MOU/740/2025	UNDIP/MOU/355/2025	Nota Kesepahaman antara Universitas Diponegoro dan Badan Riset dan Inovasi Nasional (BRIN) tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat	5	2025-10-15	2025-11-15	2026-07-15 23:24:06	2026-07-15 23:24:06	Ellis Zelda Winarsih - (+62) 595 0481 152	2025-10-15	\N	\N	\N
75	26	3	\N	\N	Implementation Arrangement: Kuliah Tamu oleh Praktisi dari Badan Riset dan Inovasi Nasional (BRIN)	1	2026-06-17	\N	2026-07-15 23:24:06	2026-07-15 23:24:06	Yosef Darmaji Dongoran - 0346 1093 134	2026-06-17	\N	\N	\N
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: jenis_dokumen; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.jenis_dokumen (id, nama, created_at, updated_at) FROM stdin;
1	Memorandum of Understanding (MoU)	\N	\N
2	Memorandum of Agreement (MoA)	\N	\N
3	Implementation Arrangement (IA)	\N	\N
4	Memorandum of Understanding (MoU)	\N	\N
5	Memorandum of Agreement (MoA)	\N	\N
6	Implementation Arrangement (IA)	\N	\N
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: klasifikasi_mitra; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.klasifikasi_mitra (id, nama, created_at, updated_at) FROM stdin;
1	Perusahaan Multinasional	2026-07-14 18:36:52	2026-07-14 18:36:52
2	Perusahaan Nasional Berstandar Tinggi	2026-07-14 18:36:52	2026-07-14 18:36:52
3	Perusahaan Teknologi Global	2026-07-14 18:36:52	2026-07-14 18:36:52
4	Perusahaan Rintisan (Startup) Teknologi	2026-07-14 18:36:52	2026-07-14 18:36:52
5	Organisasi Nirlaba Kelas Dunia	2026-07-14 18:36:52	2026-07-14 18:36:52
6	Institusi atau Organisasi Multilateral	2026-07-14 18:36:52	2026-07-14 18:36:52
7	Perguruan Tinggi Luar Negeri yang Masuk dalam Daftar QS Top 200 Berdasarkan Bidang Ilmu	2026-07-14 18:36:52	2026-07-14 18:36:52
8	Perguruan Tinggi Dalam Negeri yang Masuk dalam Daftar QS Top 200 Berdasarkan Bidang Ilmu	2026-07-14 18:36:52	2026-07-14 18:36:52
9	Instansi Pemerintah Pusat dan/atau Daerah, BUMN, dan/atau BUMD	2026-07-14 18:36:52	2026-07-14 18:36:52
10	Rumah Sakit	2026-07-14 18:36:52	2026-07-14 18:36:52
11	Dunia Usaha	2026-07-14 18:36:52	2026-07-14 18:36:52
12	Institusi Pendidikan	2026-07-14 18:36:52	2026-07-14 18:36:52
13	Organisasi, Perguruan Tinggi, Fakultas, atau Program Studi dalam Bidang yang Relevan	2026-07-14 18:36:52	2026-07-14 18:36:52
14	Lembaga Riset Pemerintah, Swasta, Nasional, maupun Internasional	2026-07-14 18:36:52	2026-07-14 18:36:52
15	Lembaga Kebudayaan Berskala Nasional atau Bereputasi Internasional	2026-07-14 18:36:52	2026-07-14 18:36:52
16	Belum ditentukan	2026-07-14 18:36:52	2026-07-14 18:36:52
17	Perusahaan Multinasional	2026-07-15 23:24:04	2026-07-15 23:24:04
18	Perusahaan Nasional Berstandar Tinggi	2026-07-15 23:24:04	2026-07-15 23:24:04
19	Perusahaan Teknologi Global	2026-07-15 23:24:04	2026-07-15 23:24:04
20	Perusahaan Rintisan (Startup) Teknologi	2026-07-15 23:24:04	2026-07-15 23:24:04
21	Organisasi Nirlaba Kelas Dunia	2026-07-15 23:24:04	2026-07-15 23:24:04
22	Institusi atau Organisasi Multilateral	2026-07-15 23:24:04	2026-07-15 23:24:04
23	Perguruan Tinggi Luar Negeri yang Masuk dalam Daftar QS Top 200 Berdasarkan Bidang Ilmu	2026-07-15 23:24:04	2026-07-15 23:24:04
24	Perguruan Tinggi Dalam Negeri yang Masuk dalam Daftar QS Top 200 Berdasarkan Bidang Ilmu	2026-07-15 23:24:04	2026-07-15 23:24:04
25	Instansi Pemerintah Pusat dan/atau Daerah, BUMN, dan/atau BUMD	2026-07-15 23:24:04	2026-07-15 23:24:04
26	Rumah Sakit	2026-07-15 23:24:04	2026-07-15 23:24:04
27	Dunia Usaha	2026-07-15 23:24:04	2026-07-15 23:24:04
28	Institusi Pendidikan	2026-07-15 23:24:04	2026-07-15 23:24:04
29	Organisasi, Perguruan Tinggi, Fakultas, atau Program Studi dalam Bidang yang Relevan	2026-07-15 23:24:04	2026-07-15 23:24:04
30	Lembaga Riset Pemerintah, Swasta, Nasional, maupun Internasional	2026-07-15 23:24:04	2026-07-15 23:24:04
31	Lembaga Kebudayaan Berskala Nasional atau Bereputasi Internasional	2026-07-15 23:24:04	2026-07-15 23:24:04
32	Belum ditentukan	2026-07-15 23:24:04	2026-07-15 23:24:04
\.


--
-- Data for Name: log; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.log (id, user_id, mitra_id, dokumen_id, keterangan, tanggal_log, created_at, updated_at, unit_id) FROM stdin;
1	3	1	1	Dokumen diinisiasi dan masuk ke sistem	2025-09-14	2025-09-14 00:00:00	2025-09-14 00:00:00	5
2	1	1	1	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-09-16	2025-09-16 00:00:00	2025-09-16 00:00:00	2
3	2	1	1	Dokumen resmi diterbitkan dan diarsipkan	2025-10-14	2025-10-14 00:00:00	2025-10-14 00:00:00	6
4	3	1	2	Dokumen diinisiasi dan masuk ke sistem	2026-06-14	2026-06-14 00:00:00	2026-06-14 00:00:00	2
5	2	1	2	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-06-18	2026-06-18 00:00:00	2026-06-18 00:00:00	6
6	1	1	3	Dokumen diinisiasi dan masuk ke sistem	2026-06-02	2026-06-02 00:00:00	2026-06-02 00:00:00	1
7	1	2	4	Dokumen diinisiasi dan masuk ke sistem	2025-08-14	2025-08-14 00:00:00	2025-08-14 00:00:00	2
8	2	2	4	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-19	2025-08-19 00:00:00	2025-08-19 00:00:00	5
9	1	2	4	Dokumen resmi diterbitkan dan diarsipkan	2025-09-14	2025-09-14 00:00:00	2025-09-14 00:00:00	5
10	2	2	5	Dokumen diinisiasi dan masuk ke sistem	2026-05-26	2026-05-26 00:00:00	2026-05-26 00:00:00	5
11	1	3	6	Dokumen diinisiasi dan masuk ke sistem	2026-01-14	2026-01-14 00:00:00	2026-01-14 00:00:00	5
12	2	3	6	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-01-19	2026-01-19 00:00:00	2026-01-19 00:00:00	4
13	3	3	6	Dokumen resmi diterbitkan dan diarsipkan	2026-02-14	2026-02-14 00:00:00	2026-02-14 00:00:00	4
14	3	3	7	Dokumen diinisiasi dan masuk ke sistem	2026-06-14	2026-06-14 00:00:00	2026-06-14 00:00:00	3
15	3	3	8	Dokumen diinisiasi dan masuk ke sistem	2026-06-09	2026-06-09 00:00:00	2026-06-09 00:00:00	2
16	2	4	9	Dokumen diinisiasi dan masuk ke sistem	2026-01-14	2026-01-14 00:00:00	2026-01-14 00:00:00	4
17	1	4	9	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-01-18	2026-01-18 00:00:00	2026-01-18 00:00:00	2
18	2	4	9	Dokumen resmi diterbitkan dan diarsipkan	2026-02-14	2026-02-14 00:00:00	2026-02-14 00:00:00	6
19	1	5	10	Dokumen diinisiasi dan masuk ke sistem	2025-10-14	2025-10-14 00:00:00	2025-10-14 00:00:00	6
20	2	5	10	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-16	2025-10-16 00:00:00	2025-10-16 00:00:00	3
21	2	5	10	Dokumen resmi diterbitkan dan diarsipkan	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	1
22	2	6	11	Dokumen diinisiasi dan masuk ke sistem	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	1
23	1	6	11	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-18	2025-11-18 00:00:00	2025-11-18 00:00:00	3
24	1	6	11	Dokumen resmi diterbitkan dan diarsipkan	2025-12-14	2025-12-14 00:00:00	2025-12-14 00:00:00	2
25	2	6	12	Dokumen diinisiasi dan masuk ke sistem	2026-06-30	2026-06-30 00:00:00	2026-06-30 00:00:00	5
26	3	7	13	Dokumen diinisiasi dan masuk ke sistem	2025-10-14	2025-10-14 00:00:00	2025-10-14 00:00:00	1
27	1	7	13	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-18	2025-10-18 00:00:00	2025-10-18 00:00:00	3
28	3	7	13	Dokumen resmi diterbitkan dan diarsipkan	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	5
29	3	8	14	Dokumen diinisiasi dan masuk ke sistem	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	6
30	2	8	14	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-16	2025-11-16 00:00:00	2025-11-16 00:00:00	2
31	2	8	14	Dokumen resmi diterbitkan dan diarsipkan	2025-12-14	2025-12-14 00:00:00	2025-12-14 00:00:00	3
32	1	8	15	Dokumen diinisiasi dan masuk ke sistem	2026-03-14	2026-03-14 00:00:00	2026-03-14 00:00:00	5
33	3	8	15	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-03-19	2026-03-19 00:00:00	2026-03-19 00:00:00	1
34	1	8	16	Dokumen diinisiasi dan masuk ke sistem	2026-06-23	2026-06-23 00:00:00	2026-06-23 00:00:00	1
35	3	9	17	Dokumen diinisiasi dan masuk ke sistem	2025-08-14	2025-08-14 00:00:00	2025-08-14 00:00:00	3
36	2	9	17	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-16	2025-08-16 00:00:00	2025-08-16 00:00:00	5
37	3	9	17	Dokumen resmi diterbitkan dan diarsipkan	2025-09-14	2025-09-14 00:00:00	2025-09-14 00:00:00	4
38	2	10	18	Dokumen diinisiasi dan masuk ke sistem	2025-08-14	2025-08-14 00:00:00	2025-08-14 00:00:00	2
39	2	10	18	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-18	2025-08-18 00:00:00	2025-08-18 00:00:00	5
40	2	10	18	Dokumen resmi diterbitkan dan diarsipkan	2025-09-14	2025-09-14 00:00:00	2025-09-14 00:00:00	5
41	3	10	19	Dokumen diinisiasi dan masuk ke sistem	2026-06-14	2026-06-14 00:00:00	2026-06-14 00:00:00	3
42	2	10	19	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-06-18	2026-06-18 00:00:00	2026-06-18 00:00:00	6
43	2	11	20	Dokumen diinisiasi dan masuk ke sistem	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	3
44	1	11	20	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-16	2025-11-16 00:00:00	2025-11-16 00:00:00	2
45	2	11	20	Dokumen resmi diterbitkan dan diarsipkan	2025-12-14	2025-12-14 00:00:00	2025-12-14 00:00:00	2
46	3	11	21	Dokumen diinisiasi dan masuk ke sistem	2026-03-14	2026-03-14 00:00:00	2026-03-14 00:00:00	5
47	1	11	22	Dokumen diinisiasi dan masuk ke sistem	2026-06-09	2026-06-09 00:00:00	2026-06-09 00:00:00	5
48	3	12	23	Dokumen diinisiasi dan masuk ke sistem	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	5
49	3	12	23	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-16	2025-11-16 00:00:00	2025-11-16 00:00:00	6
50	1	12	23	Dokumen resmi diterbitkan dan diarsipkan	2025-12-14	2025-12-14 00:00:00	2025-12-14 00:00:00	1
51	2	12	24	Dokumen diinisiasi dan masuk ke sistem	2026-04-14	2026-04-14 00:00:00	2026-04-14 00:00:00	6
52	1	12	24	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-04-17	2026-04-17 00:00:00	2026-04-17 00:00:00	4
53	2	12	25	Dokumen diinisiasi dan masuk ke sistem	2026-06-30	2026-06-30 00:00:00	2026-06-30 00:00:00	4
54	1	13	26	Dokumen diinisiasi dan masuk ke sistem	2025-10-14	2025-10-14 00:00:00	2025-10-14 00:00:00	1
55	1	13	26	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-17	2025-10-17 00:00:00	2025-10-17 00:00:00	4
56	1	13	26	Dokumen resmi diterbitkan dan diarsipkan	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	5
57	3	1	1	Dokumen diinisiasi dan masuk ke sistem	2025-09-14	2025-09-14 00:00:00	2025-09-14 00:00:00	8
58	1	1	1	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-09-19	2025-09-19 00:00:00	2025-09-19 00:00:00	3
59	1	1	1	Dokumen resmi diterbitkan dan diarsipkan	2025-10-14	2025-10-14 00:00:00	2025-10-14 00:00:00	9
60	1	1	2	Dokumen diinisiasi dan masuk ke sistem	2026-06-14	2026-06-14 00:00:00	2026-06-14 00:00:00	1
61	1	1	2	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-06-17	2026-06-17 00:00:00	2026-06-17 00:00:00	6
62	2	1	3	Dokumen diinisiasi dan masuk ke sistem	2026-06-02	2026-06-02 00:00:00	2026-06-02 00:00:00	12
63	3	2	4	Dokumen diinisiasi dan masuk ke sistem	2025-08-14	2025-08-14 00:00:00	2025-08-14 00:00:00	2
64	2	2	4	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-18	2025-08-18 00:00:00	2025-08-18 00:00:00	3
65	2	2	4	Dokumen resmi diterbitkan dan diarsipkan	2025-09-14	2025-09-14 00:00:00	2025-09-14 00:00:00	3
66	3	2	5	Dokumen diinisiasi dan masuk ke sistem	2026-05-26	2026-05-26 00:00:00	2026-05-26 00:00:00	4
67	1	3	6	Dokumen diinisiasi dan masuk ke sistem	2026-01-14	2026-01-14 00:00:00	2026-01-14 00:00:00	10
68	3	3	6	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-01-17	2026-01-17 00:00:00	2026-01-17 00:00:00	11
69	1	3	6	Dokumen resmi diterbitkan dan diarsipkan	2026-02-14	2026-02-14 00:00:00	2026-02-14 00:00:00	7
70	2	3	7	Dokumen diinisiasi dan masuk ke sistem	2026-06-14	2026-06-14 00:00:00	2026-06-14 00:00:00	12
71	2	3	8	Dokumen diinisiasi dan masuk ke sistem	2026-06-09	2026-06-09 00:00:00	2026-06-09 00:00:00	7
72	3	4	9	Dokumen diinisiasi dan masuk ke sistem	2026-01-14	2026-01-14 00:00:00	2026-01-14 00:00:00	2
73	2	4	9	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-01-19	2026-01-19 00:00:00	2026-01-19 00:00:00	10
74	3	4	9	Dokumen resmi diterbitkan dan diarsipkan	2026-02-14	2026-02-14 00:00:00	2026-02-14 00:00:00	10
75	1	5	10	Dokumen diinisiasi dan masuk ke sistem	2025-10-14	2025-10-14 00:00:00	2025-10-14 00:00:00	1
76	3	5	10	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-17	2025-10-17 00:00:00	2025-10-17 00:00:00	10
77	1	5	10	Dokumen resmi diterbitkan dan diarsipkan	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	6
78	2	6	11	Dokumen diinisiasi dan masuk ke sistem	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	2
79	2	6	11	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-18	2025-11-18 00:00:00	2025-11-18 00:00:00	11
80	1	6	11	Dokumen resmi diterbitkan dan diarsipkan	2025-12-14	2025-12-14 00:00:00	2025-12-14 00:00:00	7
81	2	6	12	Dokumen diinisiasi dan masuk ke sistem	2026-06-30	2026-06-30 00:00:00	2026-06-30 00:00:00	6
82	2	7	13	Dokumen diinisiasi dan masuk ke sistem	2025-10-14	2025-10-14 00:00:00	2025-10-14 00:00:00	8
83	2	7	13	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-17	2025-10-17 00:00:00	2025-10-17 00:00:00	10
84	2	7	13	Dokumen resmi diterbitkan dan diarsipkan	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	4
85	1	8	14	Dokumen diinisiasi dan masuk ke sistem	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	12
86	3	8	14	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-17	2025-11-17 00:00:00	2025-11-17 00:00:00	11
87	2	8	14	Dokumen resmi diterbitkan dan diarsipkan	2025-12-14	2025-12-14 00:00:00	2025-12-14 00:00:00	3
88	3	8	15	Dokumen diinisiasi dan masuk ke sistem	2026-03-14	2026-03-14 00:00:00	2026-03-14 00:00:00	12
89	3	8	15	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-03-16	2026-03-16 00:00:00	2026-03-16 00:00:00	7
90	2	8	16	Dokumen diinisiasi dan masuk ke sistem	2026-06-23	2026-06-23 00:00:00	2026-06-23 00:00:00	11
91	3	9	17	Dokumen diinisiasi dan masuk ke sistem	2025-08-14	2025-08-14 00:00:00	2025-08-14 00:00:00	4
92	2	9	17	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-16	2025-08-16 00:00:00	2025-08-16 00:00:00	7
93	1	9	17	Dokumen resmi diterbitkan dan diarsipkan	2025-09-14	2025-09-14 00:00:00	2025-09-14 00:00:00	7
94	2	10	18	Dokumen diinisiasi dan masuk ke sistem	2025-08-14	2025-08-14 00:00:00	2025-08-14 00:00:00	5
95	2	10	18	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-16	2025-08-16 00:00:00	2025-08-16 00:00:00	11
96	2	10	18	Dokumen resmi diterbitkan dan diarsipkan	2025-09-14	2025-09-14 00:00:00	2025-09-14 00:00:00	8
97	1	10	19	Dokumen diinisiasi dan masuk ke sistem	2026-06-14	2026-06-14 00:00:00	2026-06-14 00:00:00	9
98	3	10	19	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-06-19	2026-06-19 00:00:00	2026-06-19 00:00:00	6
99	2	11	20	Dokumen diinisiasi dan masuk ke sistem	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	3
100	2	11	20	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-16	2025-11-16 00:00:00	2025-11-16 00:00:00	11
101	3	11	20	Dokumen resmi diterbitkan dan diarsipkan	2025-12-14	2025-12-14 00:00:00	2025-12-14 00:00:00	3
102	3	11	21	Dokumen diinisiasi dan masuk ke sistem	2026-03-14	2026-03-14 00:00:00	2026-03-14 00:00:00	2
103	2	11	22	Dokumen diinisiasi dan masuk ke sistem	2026-06-09	2026-06-09 00:00:00	2026-06-09 00:00:00	11
104	1	12	23	Dokumen diinisiasi dan masuk ke sistem	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	5
105	3	12	23	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-19	2025-11-19 00:00:00	2025-11-19 00:00:00	12
106	1	12	23	Dokumen resmi diterbitkan dan diarsipkan	2025-12-14	2025-12-14 00:00:00	2025-12-14 00:00:00	7
107	1	12	24	Dokumen diinisiasi dan masuk ke sistem	2026-04-14	2026-04-14 00:00:00	2026-04-14 00:00:00	5
108	1	12	24	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-04-16	2026-04-16 00:00:00	2026-04-16 00:00:00	1
109	2	12	25	Dokumen diinisiasi dan masuk ke sistem	2026-06-30	2026-06-30 00:00:00	2026-06-30 00:00:00	11
110	3	13	26	Dokumen diinisiasi dan masuk ke sistem	2025-10-14	2025-10-14 00:00:00	2025-10-14 00:00:00	11
111	1	13	26	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-18	2025-10-18 00:00:00	2025-10-18 00:00:00	9
112	1	13	26	Dokumen resmi diterbitkan dan diarsipkan	2025-11-14	2025-11-14 00:00:00	2025-11-14 00:00:00	6
113	2	1	27	Dokumen diinisiasi dan masuk ke sistem	2025-12-15	2025-12-15 00:00:00	2025-12-15 00:00:00	5
114	3	1	27	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-12-19	2025-12-19 00:00:00	2025-12-19 00:00:00	2
115	1	1	27	Dokumen resmi diterbitkan dan diarsipkan	2026-01-15	2026-01-15 00:00:00	2026-01-15 00:00:00	5
116	3	2	28	Dokumen diinisiasi dan masuk ke sistem	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	2
117	2	2	28	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-09-20	2025-09-20 00:00:00	2025-09-20 00:00:00	6
118	3	2	28	Dokumen resmi diterbitkan dan diarsipkan	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	12
119	2	2	29	Dokumen diinisiasi dan masuk ke sistem	2026-04-15	2026-04-15 00:00:00	2026-04-15 00:00:00	2
120	3	2	29	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-04-17	2026-04-17 00:00:00	2026-04-17 00:00:00	5
121	2	2	30	Dokumen diinisiasi dan masuk ke sistem	2026-05-27	2026-05-27 00:00:00	2026-05-27 00:00:00	10
122	3	3	31	Dokumen diinisiasi dan masuk ke sistem	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	12
123	3	3	31	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-09-18	2025-09-18 00:00:00	2025-09-18 00:00:00	2
124	2	3	31	Dokumen resmi diterbitkan dan diarsipkan	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	3
125	3	4	32	Dokumen diinisiasi dan masuk ke sistem	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	4
126	3	4	32	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-20	2025-08-20 00:00:00	2025-08-20 00:00:00	7
127	2	4	32	Dokumen resmi diterbitkan dan diarsipkan	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	4
128	1	4	33	Dokumen diinisiasi dan masuk ke sistem	2026-02-15	2026-02-15 00:00:00	2026-02-15 00:00:00	4
129	1	4	33	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-02-18	2026-02-18 00:00:00	2026-02-18 00:00:00	10
130	1	4	34	Dokumen diinisiasi dan masuk ke sistem	2026-07-01	2026-07-01 00:00:00	2026-07-01 00:00:00	6
131	2	5	35	Dokumen diinisiasi dan masuk ke sistem	2025-07-15	2025-07-15 00:00:00	2025-07-15 00:00:00	9
132	2	5	35	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-07-19	2025-07-19 00:00:00	2025-07-19 00:00:00	4
133	1	5	35	Dokumen resmi diterbitkan dan diarsipkan	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	1
134	2	5	36	Dokumen diinisiasi dan masuk ke sistem	2026-04-15	2026-04-15 00:00:00	2026-04-15 00:00:00	12
135	2	5	36	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-04-18	2026-04-18 00:00:00	2026-04-18 00:00:00	8
136	3	6	37	Dokumen diinisiasi dan masuk ke sistem	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	12
137	2	6	37	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-09-19	2025-09-19 00:00:00	2025-09-19 00:00:00	1
138	1	6	37	Dokumen resmi diterbitkan dan diarsipkan	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	9
139	1	6	38	Dokumen diinisiasi dan masuk ke sistem	2026-02-15	2026-02-15 00:00:00	2026-02-15 00:00:00	9
140	3	6	38	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-02-17	2026-02-17 00:00:00	2026-02-17 00:00:00	11
141	2	6	39	Dokumen diinisiasi dan masuk ke sistem	2026-06-03	2026-06-03 00:00:00	2026-06-03 00:00:00	2
142	2	7	40	Dokumen diinisiasi dan masuk ke sistem	2025-07-15	2025-07-15 00:00:00	2025-07-15 00:00:00	7
143	1	7	40	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-07-19	2025-07-19 00:00:00	2025-07-19 00:00:00	1
144	3	7	40	Dokumen resmi diterbitkan dan diarsipkan	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	11
145	1	7	41	Dokumen diinisiasi dan masuk ke sistem	2026-03-15	2026-03-15 00:00:00	2026-03-15 00:00:00	11
146	2	7	41	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-03-17	2026-03-17 00:00:00	2026-03-17 00:00:00	4
147	1	8	42	Dokumen diinisiasi dan masuk ke sistem	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	5
148	1	8	42	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-19	2025-10-19 00:00:00	2025-10-19 00:00:00	4
149	1	8	42	Dokumen resmi diterbitkan dan diarsipkan	2025-11-15	2025-11-15 00:00:00	2025-11-15 00:00:00	12
150	2	9	43	Dokumen diinisiasi dan masuk ke sistem	2025-07-15	2025-07-15 00:00:00	2025-07-15 00:00:00	2
151	2	9	43	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-07-18	2025-07-18 00:00:00	2025-07-18 00:00:00	4
152	2	9	43	Dokumen resmi diterbitkan dan diarsipkan	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	9
153	2	9	44	Dokumen diinisiasi dan masuk ke sistem	2026-05-27	2026-05-27 00:00:00	2026-05-27 00:00:00	10
154	2	10	45	Dokumen diinisiasi dan masuk ke sistem	2025-07-15	2025-07-15 00:00:00	2025-07-15 00:00:00	2
155	3	10	45	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-07-18	2025-07-18 00:00:00	2025-07-18 00:00:00	5
156	1	10	45	Dokumen resmi diterbitkan dan diarsipkan	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	4
157	1	10	46	Dokumen diinisiasi dan masuk ke sistem	2026-03-15	2026-03-15 00:00:00	2026-03-15 00:00:00	6
158	1	10	46	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-03-18	2026-03-18 00:00:00	2026-03-18 00:00:00	9
159	2	10	47	Dokumen diinisiasi dan masuk ke sistem	2026-06-03	2026-06-03 00:00:00	2026-06-03 00:00:00	7
160	1	11	48	Dokumen diinisiasi dan masuk ke sistem	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	2
161	2	11	48	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-17	2025-08-17 00:00:00	2025-08-17 00:00:00	12
162	1	11	48	Dokumen resmi diterbitkan dan diarsipkan	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	6
163	1	12	49	Dokumen diinisiasi dan masuk ke sistem	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	4
164	3	12	49	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-20	2025-08-20 00:00:00	2025-08-20 00:00:00	4
165	2	12	49	Dokumen resmi diterbitkan dan diarsipkan	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	2
166	2	13	50	Dokumen diinisiasi dan masuk ke sistem	2026-01-15	2026-01-15 00:00:00	2026-01-15 00:00:00	5
167	3	13	50	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-01-20	2026-01-20 00:00:00	2026-01-20 00:00:00	4
168	1	13	50	Dokumen resmi diterbitkan dan diarsipkan	2026-02-15	2026-02-15 00:00:00	2026-02-15 00:00:00	10
169	2	13	51	Dokumen diinisiasi dan masuk ke sistem	2026-05-15	2026-05-15 00:00:00	2026-05-15 00:00:00	11
170	3	13	51	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-05-20	2026-05-20 00:00:00	2026-05-20 00:00:00	10
171	1	14	52	Dokumen diinisiasi dan masuk ke sistem	2025-07-15	2025-07-15 00:00:00	2025-07-15 00:00:00	2
172	2	14	52	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-07-17	2025-07-17 00:00:00	2025-07-17 00:00:00	3
173	2	14	52	Dokumen resmi diterbitkan dan diarsipkan	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	5
174	3	14	53	Dokumen diinisiasi dan masuk ke sistem	2026-06-17	2026-06-17 00:00:00	2026-06-17 00:00:00	5
175	1	15	54	Dokumen diinisiasi dan masuk ke sistem	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	4
176	3	15	54	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-08-18	2025-08-18 00:00:00	2025-08-18 00:00:00	5
177	3	15	54	Dokumen resmi diterbitkan dan diarsipkan	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	3
178	3	15	55	Dokumen diinisiasi dan masuk ke sistem	2026-05-15	2026-05-15 00:00:00	2026-05-15 00:00:00	4
179	2	15	55	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-05-19	2026-05-19 00:00:00	2026-05-19 00:00:00	11
180	2	16	56	Dokumen diinisiasi dan masuk ke sistem	2026-01-15	2026-01-15 00:00:00	2026-01-15 00:00:00	8
181	1	16	56	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-01-19	2026-01-19 00:00:00	2026-01-19 00:00:00	12
182	1	16	56	Dokumen resmi diterbitkan dan diarsipkan	2026-02-15	2026-02-15 00:00:00	2026-02-15 00:00:00	10
183	2	16	57	Dokumen diinisiasi dan masuk ke sistem	2026-04-15	2026-04-15 00:00:00	2026-04-15 00:00:00	2
184	3	16	57	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-04-20	2026-04-20 00:00:00	2026-04-20 00:00:00	5
185	3	17	58	Dokumen diinisiasi dan masuk ke sistem	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	6
186	1	17	58	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-09-20	2025-09-20 00:00:00	2025-09-20 00:00:00	1
187	3	17	58	Dokumen resmi diterbitkan dan diarsipkan	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	2
188	1	17	59	Dokumen diinisiasi dan masuk ke sistem	2026-06-03	2026-06-03 00:00:00	2026-06-03 00:00:00	2
189	2	18	60	Dokumen diinisiasi dan masuk ke sistem	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	3
190	2	18	60	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-09-17	2025-09-17 00:00:00	2025-09-17 00:00:00	7
191	3	18	60	Dokumen resmi diterbitkan dan diarsipkan	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	3
192	1	19	61	Dokumen diinisiasi dan masuk ke sistem	2025-12-15	2025-12-15 00:00:00	2025-12-15 00:00:00	7
193	3	19	61	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-12-19	2025-12-19 00:00:00	2025-12-19 00:00:00	5
194	1	19	61	Dokumen resmi diterbitkan dan diarsipkan	2026-01-15	2026-01-15 00:00:00	2026-01-15 00:00:00	9
195	3	20	62	Dokumen diinisiasi dan masuk ke sistem	2025-11-15	2025-11-15 00:00:00	2025-11-15 00:00:00	1
196	3	20	62	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-19	2025-11-19 00:00:00	2025-11-19 00:00:00	12
197	1	20	62	Dokumen resmi diterbitkan dan diarsipkan	2025-12-15	2025-12-15 00:00:00	2025-12-15 00:00:00	12
198	3	20	63	Dokumen diinisiasi dan masuk ke sistem	2026-06-15	2026-06-15 00:00:00	2026-06-15 00:00:00	6
199	3	20	63	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2026-06-19	2026-06-19 00:00:00	2026-06-19 00:00:00	4
200	3	20	64	Dokumen diinisiasi dan masuk ke sistem	2026-06-24	2026-06-24 00:00:00	2026-06-24 00:00:00	11
201	1	21	65	Dokumen diinisiasi dan masuk ke sistem	2025-09-15	2025-09-15 00:00:00	2025-09-15 00:00:00	11
202	2	21	65	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-09-20	2025-09-20 00:00:00	2025-09-20 00:00:00	4
203	1	21	65	Dokumen resmi diterbitkan dan diarsipkan	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	1
204	1	21	66	Dokumen diinisiasi dan masuk ke sistem	2026-06-03	2026-06-03 00:00:00	2026-06-03 00:00:00	10
205	3	22	67	Dokumen diinisiasi dan masuk ke sistem	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	3
206	1	22	67	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-20	2025-10-20 00:00:00	2025-10-20 00:00:00	4
207	3	22	67	Dokumen resmi diterbitkan dan diarsipkan	2025-11-15	2025-11-15 00:00:00	2025-11-15 00:00:00	5
208	2	23	68	Dokumen diinisiasi dan masuk ke sistem	2025-11-15	2025-11-15 00:00:00	2025-11-15 00:00:00	6
209	2	23	68	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-11-19	2025-11-19 00:00:00	2025-11-19 00:00:00	1
210	2	23	68	Dokumen resmi diterbitkan dan diarsipkan	2025-12-15	2025-12-15 00:00:00	2025-12-15 00:00:00	3
211	2	24	69	Dokumen diinisiasi dan masuk ke sistem	2025-12-15	2025-12-15 00:00:00	2025-12-15 00:00:00	7
212	1	24	69	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-12-17	2025-12-17 00:00:00	2025-12-17 00:00:00	11
213	1	24	69	Dokumen resmi diterbitkan dan diarsipkan	2026-01-15	2026-01-15 00:00:00	2026-01-15 00:00:00	6
214	3	24	70	Dokumen diinisiasi dan masuk ke sistem	2026-07-08	2026-07-08 00:00:00	2026-07-08 00:00:00	5
215	2	25	71	Dokumen diinisiasi dan masuk ke sistem	2025-07-15	2025-07-15 00:00:00	2025-07-15 00:00:00	1
216	2	25	71	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-07-18	2025-07-18 00:00:00	2025-07-18 00:00:00	3
217	3	25	71	Dokumen resmi diterbitkan dan diarsipkan	2025-08-15	2025-08-15 00:00:00	2025-08-15 00:00:00	8
218	3	25	72	Dokumen diinisiasi dan masuk ke sistem	2026-02-15	2026-02-15 00:00:00	2026-02-15 00:00:00	11
219	2	25	73	Dokumen diinisiasi dan masuk ke sistem	2026-06-03	2026-06-03 00:00:00	2026-06-03 00:00:00	5
220	3	26	74	Dokumen diinisiasi dan masuk ke sistem	2025-10-15	2025-10-15 00:00:00	2025-10-15 00:00:00	6
221	2	26	74	Draf dokumen diperiksa oleh bagian hukum/kerjasama	2025-10-17	2025-10-17 00:00:00	2025-10-17 00:00:00	5
222	1	26	74	Dokumen resmi diterbitkan dan diarsipkan	2025-11-15	2025-11-15 00:00:00	2025-11-15 00:00:00	12
223	2	26	75	Dokumen diinisiasi dan masuk ke sistem	2026-06-17	2026-06-17 00:00:00	2026-06-17 00:00:00	5
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000001_create_cache_table	1
2	0001_01_01_000002_create_jobs_table	1
3	2026_01_01_205706_create_personal_access_tokens_table	1
4	2026_01_02_171008_create_jenis_dokumen	1
5	2026_01_02_171035_create_klasifikasi_mitra	1
6	2026_01_02_171054_create_mitra	1
7	2026_01_02_171110_create_status	1
8	2026_01_02_174549_create_roles	1
9	2026_01_02_175348_create_users	1
10	2026_01_02_181010_create_dokumen	1
11	2026_01_02_181554_create_log	1
12	2026_01_22_185217_create_activity_logs_table	1
13	2026_01_29_164951_add_indexes_to_dokumen_table	1
14	2026_01_29_170141_add_indexes_to_mitra_and_log_tables	1
15	2026_02_04_183700_create_unit	1
16	2026_02_04_203114_add_status_to_mitra_table	1
17	2026_02_04_204823_move_contact_person_from_log_to_dokumen	1
18	2026_02_04_231012_add_unit_id_to_log_table	1
19	2026_02_04_234406_add_tanggal_dokumen_to_dokumen_table	1
20	2026_02_08_124500_add_draft_and_final_dokumen_to_dokumen_table	1
21	2026_02_09_000000_add_operator_role	1
22	2026_05_06_000000_add_deleted_at_to_users_table	1
23	2026_05_09_000000_safely_archive_master_data	1
24	2026_05_09_000001_add_deleted_at_indexes_to_master_data	1
25	2026_05_09_110000_add_indexes_to_activity_logs_table	1
26	2026_05_12_000000_add_user_id_to_dokumen_table	1
27	2026_01_02_175645_create_user_roles	2
28	2026_05_23_000000_add_account_status_to_users_table	2
\.


--
-- Data for Name: mitra; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.mitra (id, nama, klasifikasi_mitra_id, alamat, contact_person, created_at, updated_at, status, deleted_at) FROM stdin;
1	PT Gojek Indonesia	4	Gedung Pasaraya Blok M, Jl. Iskandarsyah II No. 2, Jakarta Selatan	Budi Santoso/081234567890	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
2	PT Telekomunikasi Indonesia (Persero) Tbk	9	Jl. Japati No. 1, Bandung, Jawa Barat	Siti Aminah/081298765432	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
3	PT Pertamina (Persero)	9	Jl. Medan Merdeka Timur No. 1A, Jakarta Pusat	Rudi Hartono/081345678901	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
4	Google Indonesia	3	Pacific Century Place Tower Level 45, SCBD, Jakarta	Jason Smith/081987654321	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
5	Microsoft Indonesia	3	Jakarta Stock Exchange Building, Tower 2, Jakarta	Maria Utami/085678901234	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
6	Universitas Indonesia (UI)	8	Kampus UI Depok, Jawa Barat	Prof. Dr. Ir. Heri Hermansyah/0217867222	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
7	Institut Teknologi Bandung (ITB)	8	Jl. Ganesha No. 10, Bandung	Sekretariat Rektorat/0222500935	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
8	Universitas Gadjah Mada (UGM)	8	Bulaksumur, Caturtunggal, Sleman, Yogyakarta	Humas UGM/0274512763	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
9	PT Bank Central Asia Tbk (BCA)	2	Menara BCA, Grand Indonesia, Jakarta	Halo BCA/1500888	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
10	PT Bank Rakyat Indonesia (Persero) Tbk	9	Gedung BRI 1, Jl. Jenderal Sudirman Kav.44-46, Jakarta	Call BRI/14017	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
11	PT Shopee International Indonesia	4	Pacific Century Place Tower, SCBD, Jakarta	HR Recruitment/02180647100	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
12	PT Tokopedia	4	Tokopedia Tower, Ciputra World 2, Jakarta	Partnership Team/02150813333	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
13	Badan Riset dan Inovasi Nasional (BRIN)	14	Jl. M.H. Thamrin No. 8, Jakarta Pusat	Sekretariat Utama/0213169999	2026-07-14 18:36:52	2026-07-14 18:36:52	Approved	\N
14	PT Gojek Indonesia	4	Gedung Pasaraya Blok M, Jl. Iskandarsyah II No. 2, Jakarta Selatan	Budi Santoso/081234567890	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
15	PT Telekomunikasi Indonesia (Persero) Tbk	9	Jl. Japati No. 1, Bandung, Jawa Barat	Siti Aminah/081298765432	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
16	PT Pertamina (Persero)	9	Jl. Medan Merdeka Timur No. 1A, Jakarta Pusat	Rudi Hartono/081345678901	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
17	Google Indonesia	3	Pacific Century Place Tower Level 45, SCBD, Jakarta	Jason Smith/081987654321	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
18	Microsoft Indonesia	3	Jakarta Stock Exchange Building, Tower 2, Jakarta	Maria Utami/085678901234	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
19	Universitas Indonesia (UI)	8	Kampus UI Depok, Jawa Barat	Prof. Dr. Ir. Heri Hermansyah/0217867222	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
20	Institut Teknologi Bandung (ITB)	8	Jl. Ganesha No. 10, Bandung	Sekretariat Rektorat/0222500935	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
21	Universitas Gadjah Mada (UGM)	8	Bulaksumur, Caturtunggal, Sleman, Yogyakarta	Humas UGM/0274512763	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
22	PT Bank Central Asia Tbk (BCA)	2	Menara BCA, Grand Indonesia, Jakarta	Halo BCA/1500888	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
23	PT Bank Rakyat Indonesia (Persero) Tbk	9	Gedung BRI 1, Jl. Jenderal Sudirman Kav.44-46, Jakarta	Call BRI/14017	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
24	PT Shopee International Indonesia	4	Pacific Century Place Tower, SCBD, Jakarta	HR Recruitment/02180647100	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
25	PT Tokopedia	4	Tokopedia Tower, Ciputra World 2, Jakarta	Partnership Team/02150813333	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
26	Badan Riset dan Inovasi Nasional (BRIN)	14	Jl. M.H. Thamrin No. 8, Jakarta Pusat	Sekretariat Utama/0213169999	2026-07-15 23:24:04	2026-07-15 23:24:04	Approved	\N
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.roles (id, nama, created_at, updated_at) FROM stdin;
2	Admin	\N	2026-07-15 23:24:03
1	Operator	2026-07-14 18:36:51	2026-07-15 23:24:03
3	Viewer	\N	2026-07-15 23:24:03
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: status; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.status (id, nama, created_at, updated_at, deleted_at) FROM stdin;
1	Inisiasi & Proses	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
2	Acc Rektor	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
3	Naskah Dikirim	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
4	Naskah Dicetak	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
5	Terbit	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
6	Pending / Batal / Proses dilanjut unit lain	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
7	Inisiasi & Proses	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
8	Acc Rektor	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
9	Naskah Dikirim	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
10	Naskah Dicetak	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
11	Terbit	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
12	Pending / Batal / Proses dilanjut unit lain	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
\.


--
-- Data for Name: unit; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.unit (id, nama, created_at, updated_at, deleted_at) FROM stdin;
1	Rektorat	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
2	Wakil Rektor I	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
3	Wakil Rektor II	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
4	Wakil Rektor III	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
5	Wakil Rektor IV	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
6	DHO	2026-07-14 18:36:52	2026-07-14 18:36:52	\N
7	Rektorat	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
8	Wakil Rektor I	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
9	Wakil Rektor II	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
10	Wakil Rektor III	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
11	Wakil Rektor IV	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
12	DHO	2026-07-15 23:24:04	2026-07-15 23:24:04	\N
\.


--
-- Data for Name: user_roles; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.user_roles (id, user_id, role_id, created_at, updated_at) FROM stdin;
1	1	2	2026-07-15 23:24:04	2026-07-15 23:24:04
2	2	1	2026-07-15 23:24:04	2026-07-15 23:24:04
3	3	3	2026-07-15 23:24:04	2026-07-15 23:24:04
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: logbook_user
--

COPY public.users (id, nama, email, password, nim_nip, role_id, remember_token, created_at, updated_at, deleted_at, account_status) FROM stdin;
1	Admin User	admin@example.com	$2y$12$/LojCAA.fnGzqdvUbRu2bezMl8urIWxGa.Znj92Ba0FcX6NnWa5CC	ADM001	2	\N	2026-07-15 23:24:04	2026-07-15 23:24:04	\N	approved
2	Operator User	operator@example.com	$2y$12$o2Yh17RNHB8beAucPMp7FeX6OFcg4ADa3PJyiMgZkEXnxthcWq4mq	OPR001	1	\N	2026-07-15 23:24:04	2026-07-15 23:24:04	\N	approved
3	Viewer User	viewer@example.com	$2y$12$PRjcWzy6GDUQhJ1d0H63B.tNItdDjI7hriT8CGKrAd3m0wlOjzwta	VWR001	3	\N	2026-07-15 23:24:04	2026-07-15 23:24:04	\N	approved
\.


--
-- Name: activity_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.activity_logs_id_seq', 1, false);


--
-- Name: dokumen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.dokumen_id_seq', 75, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jenis_dokumen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.jenis_dokumen_id_seq', 6, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: klasifikasi_mitra_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.klasifikasi_mitra_id_seq', 32, true);


--
-- Name: log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.log_id_seq', 223, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 28, true);


--
-- Name: mitra_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.mitra_id_seq', 26, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.roles_id_seq', 3, true);


--
-- Name: status_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.status_id_seq', 12, true);


--
-- Name: unit_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.unit_id_seq', 12, true);


--
-- Name: user_roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.user_roles_id_seq', 3, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: logbook_user
--

SELECT pg_catalog.setval('public.users_id_seq', 1, false);


--
-- Name: activity_logs activity_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: dokumen dokumen_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.dokumen
    ADD CONSTRAINT dokumen_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: jenis_dokumen jenis_dokumen_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.jenis_dokumen
    ADD CONSTRAINT jenis_dokumen_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: klasifikasi_mitra klasifikasi_mitra_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.klasifikasi_mitra
    ADD CONSTRAINT klasifikasi_mitra_pkey PRIMARY KEY (id);


--
-- Name: log log_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.log
    ADD CONSTRAINT log_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: mitra mitra_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.mitra
    ADD CONSTRAINT mitra_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: status status_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.status
    ADD CONSTRAINT status_pkey PRIMARY KEY (id);


--
-- Name: unit unit_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.unit
    ADD CONSTRAINT unit_pkey PRIMARY KEY (id);


--
-- Name: user_roles user_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_pkey PRIMARY KEY (id);


--
-- Name: user_roles user_roles_user_id_role_id_unique; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_user_id_role_id_unique UNIQUE (user_id, role_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: activity_logs_created_at_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX activity_logs_created_at_index ON public.activity_logs USING btree (created_at);


--
-- Name: activity_logs_user_id_created_at_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX activity_logs_user_id_created_at_index ON public.activity_logs USING btree (user_id, created_at);


--
-- Name: dokumen_created_at_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX dokumen_created_at_index ON public.dokumen USING btree (created_at);


--
-- Name: dokumen_jenis_dokumen_id_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX dokumen_jenis_dokumen_id_index ON public.dokumen USING btree (jenis_dokumen_id);


--
-- Name: dokumen_judul_dokumen_fulltext; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX dokumen_judul_dokumen_fulltext ON public.dokumen USING gin (to_tsvector('english'::regconfig, (judul_dokumen)::text));


--
-- Name: dokumen_nomor_dokumen_mitra_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX dokumen_nomor_dokumen_mitra_index ON public.dokumen USING btree (nomor_dokumen_mitra);


--
-- Name: dokumen_nomor_dokumen_undip_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX dokumen_nomor_dokumen_undip_index ON public.dokumen USING btree (nomor_dokumen_undip);


--
-- Name: dokumen_status_id_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX dokumen_status_id_index ON public.dokumen USING btree (status_id);


--
-- Name: dokumen_tanggal_masuk_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX dokumen_tanggal_masuk_index ON public.dokumen USING btree (tanggal_masuk);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: log_dokumen_id_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX log_dokumen_id_index ON public.log USING btree (dokumen_id);


--
-- Name: log_tanggal_log_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX log_tanggal_log_index ON public.log USING btree (tanggal_log);


--
-- Name: mitra_deleted_at_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX mitra_deleted_at_index ON public.mitra USING btree (deleted_at);


--
-- Name: mitra_klasifikasi_mitra_id_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX mitra_klasifikasi_mitra_id_index ON public.mitra USING btree (klasifikasi_mitra_id);


--
-- Name: mitra_nama_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX mitra_nama_index ON public.mitra USING btree (nama);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: status_deleted_at_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX status_deleted_at_index ON public.status USING btree (deleted_at);


--
-- Name: unit_deleted_at_index; Type: INDEX; Schema: public; Owner: logbook_user
--

CREATE INDEX unit_deleted_at_index ON public.unit USING btree (deleted_at);


--
-- Name: activity_logs activity_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: dokumen dokumen_jenis_dokumen_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.dokumen
    ADD CONSTRAINT dokumen_jenis_dokumen_id_foreign FOREIGN KEY (jenis_dokumen_id) REFERENCES public.jenis_dokumen(id) ON DELETE RESTRICT;


--
-- Name: dokumen dokumen_mitra_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.dokumen
    ADD CONSTRAINT dokumen_mitra_id_foreign FOREIGN KEY (mitra_id) REFERENCES public.mitra(id) ON DELETE RESTRICT;


--
-- Name: dokumen dokumen_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.dokumen
    ADD CONSTRAINT dokumen_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.status(id) ON DELETE RESTRICT;


--
-- Name: dokumen dokumen_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.dokumen
    ADD CONSTRAINT dokumen_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: log log_dokumen_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.log
    ADD CONSTRAINT log_dokumen_id_foreign FOREIGN KEY (dokumen_id) REFERENCES public.dokumen(id) ON DELETE CASCADE;


--
-- Name: log log_mitra_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.log
    ADD CONSTRAINT log_mitra_id_foreign FOREIGN KEY (mitra_id) REFERENCES public.mitra(id) ON DELETE RESTRICT;


--
-- Name: log log_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.log
    ADD CONSTRAINT log_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.unit(id) ON DELETE SET NULL;


--
-- Name: log log_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.log
    ADD CONSTRAINT log_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: mitra mitra_klasifikasi_mitra_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.mitra
    ADD CONSTRAINT mitra_klasifikasi_mitra_id_foreign FOREIGN KEY (klasifikasi_mitra_id) REFERENCES public.klasifikasi_mitra(id) ON DELETE RESTRICT;


--
-- Name: user_roles user_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: user_roles user_roles_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.user_roles
    ADD CONSTRAINT user_roles_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: logbook_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict iiO5i8m1btl5DJdNisc6yfw1ACO9Ko5pvS3tpT9G7QJa9YM36k7kyksnsxUBIh4

