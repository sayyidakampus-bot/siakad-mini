<?php

require_once '../src/Auth.php';
require_once '../src/DosenRepository.php';

Auth::guard();

$repository = new DosenRepository();

$id = $_GET['id'] ?? null;

$data = $repository->find($id);

$mataKuliah = $repository->getAllMataKuliah();

$selectedMK = $repository->getMataKuliahByDosen($id);

if (!$data) {
    die("Data tidak ditemukan");
}

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {
        die("CSRF token tidak valid");
    }

    $nidn = trim($_POST['nidn']);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $program_studi = trim($_POST['program_studi']);
    $status = trim($_POST['status']);

    $mataKuliahIds = $_POST['mata_kuliah'] ?? [];

    $fotoName = $data['foto'];

    if (!empty($_FILES['foto']['name'])) {

        $tmpName = $_FILES['foto']['tmp_name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $mime = finfo_file($finfo, $tmpName);

        $allowedMime = [
            'image/jpeg',
            'image/png'
        ];

        if (!in_array($mime, $allowedMime)) {

            die("File harus JPG atau PNG");
        }

        $extension = pathinfo(
            $_FILES['foto']['name'],
            PATHINFO_EXTENSION
        );

        $fotoName = sha1(time() . rand()) . '.' . $extension;

        move_uploaded_file(
            $tmpName,
            "../uploads/" . $fotoName
        );
    }

    $repository->update($id, [
        'nidn' => $nidn,
        'nama' => $nama,
        'email' => $email,
        'program_studi' => $program_studi,
        'foto' => $fotoName,
        'status' => $status
    ]);

    $repository->logActivity(
        $_SESSION['user_id'],
        'update',
        'dosen',
        $id,
        'Mengedit data dosen'
    );

    $repository->saveMataKuliah(
        $id,
        $mataKuliahIds
    );

    unset($_SESSION['csrf_token']);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Dosen</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body{
            min-height: 100vh;
            background: linear-gradient(135deg, #4f46e5, #7c3aed, #9333ea);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            overflow-x: hidden;
        }

        body::before{
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            filter: blur(10px);
        }

        body::after{
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.10);
            border-radius: 50%;
            bottom: -100px;
            right: -100px;
            filter: blur(10px);
        }

        .container{
            width: 100%;
            max-width: 950px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 30px;
            padding: 40px;
            color: white;
            position: relative;
            z-index: 2;
        }

        .header{
            text-align: center;
            margin-bottom: 35px;
        }

        .header h1{
            font-size: 36px;
            margin-bottom: 10px;
        }

        .header p{
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }

        .form-grid{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full-width{
            grid-column: span 2;
        }

        .input-group label{
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .input-group input,
        .input-group select{
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: none;
            outline: none;
            background: rgba(255,255,255,0.15);
            color: white;
            font-size: 14px;
        }

        .input-group input::placeholder{
            color: rgba(255,255,255,0.7);
        }

        .input-group select option{
            color: black;
        }

        .preview-image{
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            margin-top: 10px;
        }

        .file-input{
            padding: 12px !important;
            cursor: pointer;
        }

        /* MATA KULIAH */

        .mk-wrapper{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 10px;
        }

        .mk-box{
            background: rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 20px;
        }

        .mk-title{
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 18px;
            color: white;
        }

        .mk-container{
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 350px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .checkbox-item{
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            background: rgba(255,255,255,0.06);
            padding: 10px 12px;
            border-radius: 12px;
            transition: 0.3s;
        }

        .checkbox-item:hover{
            background: rgba(255,255,255,0.12);
        }

        .checkbox-item input{
            width: 18px;
            height: 18px;
            accent-color: #ffffff;
        }

        /* BUTTON */

        .button-group{
            display: flex;
            gap: 15px;
            margin-top: 35px;
        }

        .btn{
            flex: 1;
            border: none;
            border-radius: 14px;
            padding: 15px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .btn-submit{
            background: white;
            color: #6d28d9;
        }

        .btn-submit:hover{
            transform: translateY(-3px);
        }

        .btn-back{
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .btn-back:hover{
            background: rgba(255,255,255,0.25);
        }

        @media(max-width: 768px){

            .container{
                padding: 25px;
            }

            .form-grid{
                grid-template-columns: 1fr;
            }

            .full-width{
                grid-column: span 1;
            }

            .mk-wrapper{
                grid-template-columns: 1fr;
            }

            .button-group{
                flex-direction: column;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <h1>Edit Dosen</h1>

        <p>
            Ubah data dosen pada sistem akademik
        </p>

    </div>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= $_SESSION['csrf_token'] ?>"
        >

        <div class="form-grid">

            <div class="input-group">

                <label>NIDN</label>

                <input
                    type="text"
                    name="nidn"
                    value="<?= htmlspecialchars($data['nidn'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

            </div>

            <div class="input-group">

                <label>Nama</label>

                <input
                    type="text"
                    name="nama"
                    value="<?= htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

            </div>

            <div class="input-group">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

            </div>

            <div class="input-group">

                <label>Program Studi</label>

                <select name="program_studi">

                    <option value="Teknik Informatika" <?= $data['program_studi'] == 'Teknik Informatika' ? 'selected' : '' ?>>
                        Teknik Informatika
                    </option>

                    <option value="Sistem Informasi" <?= $data['program_studi'] == 'Sistem Informasi' ? 'selected' : '' ?>>
                        Sistem Informasi
                    </option>

                    <option value="Teknik Elektro" <?= $data['program_studi'] == 'Teknik Elektro' ? 'selected' : '' ?>>
                        Teknik Elektro
                    </option>

                    <option value="Manajemen Informatika" <?= $data['program_studi'] == 'Manajemen Informatika' ? 'selected' : '' ?>>
                        Manajemen Informatika
                    </option>

                    <option value="Ilmu Komputer" <?= $data['program_studi'] == 'Ilmu Komputer' ? 'selected' : '' ?>>
                        Ilmu Komputer
                    </option>

                    <option value="Teknik Komputer" <?= $data['program_studi'] == 'Teknik Komputer' ? 'selected' : '' ?>>
                        Teknik Komputer
                    </option>

                    <option value="Teknologi Informasi" <?= $data['program_studi'] == 'Teknologi Informasi' ? 'selected' : '' ?>>
                        Teknologi Informasi
                    </option>

                    <option value="Rekayasa Perangkat Lunak" <?= $data['program_studi'] == 'Rekayasa Perangkat Lunak' ? 'selected' : '' ?>>
                        Rekayasa Perangkat Lunak
                    </option>

                    <option value="Sains Data" <?= $data['program_studi'] == 'Sains Data' ? 'selected' : '' ?>>
                        Sains Data
                    </option>

                    <option value="Sistem Komputer" <?= $data['program_studi'] == 'Sistem Komputer' ? 'selected' : '' ?>>
                        Sistem Komputer
                    </option>

                </select>

            </div>

            <div class="input-group">

                <label>Status</label>

                <select name="status">

                    <option value="aktif" <?= $data['status'] == 'aktif' ? 'selected' : '' ?>>
                        Aktif
                    </option>

                    <option value="nonaktif" <?= $data['status'] == 'nonaktif' ? 'selected' : '' ?>>
                        Nonaktif
                    </option>

                </select>

            </div>

            <div class="input-group">

                <label>Ganti Foto</label>

                <input
                    type="file"
                    name="foto"
                    class="file-input"
                >

            </div>

            <div class="input-group full-width">

                <label>Foto Saat Ini</label>

                <?php if ($data['foto']): ?>

                    <img
                        src="../uploads/<?= htmlspecialchars($data['foto'], ENT_QUOTES, 'UTF-8') ?>"
                        class="preview-image"
                    >

                <?php else: ?>

                    <p>Tidak ada foto</p>

                <?php endif; ?>

            </div>

            <!-- MATA KULIAH -->

            <div class="input-group full-width">

                <label>Mata Kuliah</label>

                <div class="mk-wrapper">

                    <!-- KOLOM KIRI -->

                    <div class="mk-box">

                        <div class="mk-title">
                            Mata Kuliah 1
                        </div>

                        <div class="mk-container">

                            <?php
                                $half = ceil(count($mataKuliah) / 2);
                                $leftMK = array_slice($mataKuliah, 0, $half);
                            ?>

                            <?php foreach ($leftMK as $mk): ?>

                                <label class="checkbox-item">

                                    <input
                                        type="checkbox"
                                        name="mata_kuliah[]"
                                        value="<?= $mk['id'] ?>"

                                        <?= in_array($mk['id'], $selectedMK)
                                            ? 'checked'
                                            : ''
                                        ?>
                                    >

                                    <?= htmlspecialchars($mk['nama']) ?>

                                </label>

                            <?php endforeach; ?>

                        </div>

                    </div>

                    <!-- KOLOM KANAN -->

                    <div class="mk-box">

                        <div class="mk-title">
                            Mata Kuliah 2
                        </div>

                        <div class="mk-container">

                            <?php
                                $rightMK = array_slice($mataKuliah, $half);
                            ?>

                            <?php foreach ($rightMK as $mk): ?>

                                <label class="checkbox-item">

                                    <input
                                        type="checkbox"
                                        name="mata_kuliah[]"
                                        value="<?= $mk['id'] ?>"

                                        <?= in_array($mk['id'], $selectedMK)
                                            ? 'checked'
                                            : ''
                                        ?>
                                    >

                                    <?= htmlspecialchars($mk['nama']) ?>

                                </label>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="button-group">

            <a href="index.php" class="btn btn-back">
                Kembali
            </a>

            <button 
                type="submit" 
                class="btn btn-submit"
            >
                Update Data
            </button>

        </div>

    </form>

</div>

</body>
</html>