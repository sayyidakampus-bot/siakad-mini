-- sql ini di import di PhpMyAdmin
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,                 -- HASIL password_hash(), BUKAN plaintext
    role          ENUM('admin','operator') NOT NULL DEFAULT 'operator',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data dosen (dengan soft delete)
CREATE TABLE dosen (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nidn           CHAR(10)     NOT NULL UNIQUE,
    nama           VARCHAR(100) NOT NULL,
    email          VARCHAR(120) NOT NULL UNIQUE,
    program_studi  ENUM('Teknik Informatika','Sistem Informasi','Teknik Elektro') NOT NULL,
    foto           VARCHAR(255) NULL,
    status         ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at     TIMESTAMP    NULL,                    -- SOFT DELETE: NULL = aktif, terisi = terhapus
    INDEX idx_status (status),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Program studi
CREATE TABLE program_studi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(11) NOT NULL UNIQUE,
    program_studi VARCHAR(100) NOT NULL,
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
)

-- Mata kuliah
CREATE TABLE mata_kuliah (
    id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode  VARCHAR(12)  NOT NULL UNIQUE,
    nama  VARCHAR(100) NOT NULL,
    sks   TINYINT UNSIGNED NOT NULL CHECK (sks BETWEEN 1 AND 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pivot many-to-many: 1 dosen mengampu banyak MK, 1 MK bisa diampu banyak dosen
CREATE TABLE dosen_matakuliah (
    dosen_id       INT UNSIGNED NOT NULL,
    matakuliah_id  INT UNSIGNED NOT NULL,
    semester       ENUM('Ganjil','Genap') NOT NULL,
    PRIMARY KEY (dosen_id, matakuliah_id, semester),
    FOREIGN KEY (dosen_id)      REFERENCES dosen(id)        ON DELETE CASCADE,
    FOREIGN KEY (matakuliah_id) REFERENCES mata_kuliah(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit log (Level 3) — siapa melakukan apa & kapan
CREATE TABLE activity_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NULL,
    aksi        VARCHAR(20)  NOT NULL,   -- 'create' | 'update' | 'delete' | 'restore' | 'login'
    entitas     VARCHAR(50)  NOT NULL,   -- 'dosen' | 'mata_kuliah' | ...
    entitas_id  INT UNSIGNED NULL,
    keterangan  VARCHAR(255) NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;