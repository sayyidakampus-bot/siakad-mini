<?php

require_once '../src/Auth.php';
require_once '../src/DosenRepository.php';

Auth::guard();
Auth::adminOnly();

$repository = new DosenRepository();

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

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

    $fotoName = null;

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

    $repository->create([
        'nidn' => $nidn,
        'nama' => $nama,
        'email' => $email,
        'program_studi' => $program_studi,
        'foto' => $fotoName,
        'status' => $status
    ]);

    $repository->logActivity(
        $_SESSION['user_id'],
        'create',
        'dosen',
        null,
        'Menambah dosen baru'
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

    <title>Tambah Dosen</title>

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
            position: relative;
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
            animation: float 6s ease-in-out infinite;
        }

        body::after{
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            bottom: -100px;
            right: -100px;
            filter: blur(10px);
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float{

            0%{
                transform: translateY(0px);
            }

            50%{
                transform: translateY(20px);
            }

            100%{
                transform: translateY(0px);
            }

        }

        .container{
            width: 100%;
            max-width: 650px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 30px;
            padding: 40px;
            color: white;
            position: relative;
            z-index: 2;
            animation: fadeIn 0.7s ease;
        }

        @keyframes fadeIn{

            from{
                opacity: 0;
                transform: translateY(20px);
            }

            to{
                opacity: 1;
                transform: translateY(0);
            }

        }

        .header{
            text-align: center;
            margin-bottom: 35px;
        }

        .header h1{
            font-size: 34px;
            margin-bottom: 10px;
        }

        .header p{
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }

        .input-group{
            margin-bottom: 22px;
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
            transition: 0.3s;
        }

        .input-group input::placeholder{
            color: rgba(255,255,255,0.7);
        }

        .input-group input:focus,
        .input-group select:focus{
            background: rgba(255,255,255,0.22);
            transform: scale(1.02);
            box-shadow: 0 0 10px rgba(255,255,255,0.2);
        }

        .input-group select option{
            color: black;
        }

        .file-input{
            padding: 12px !important;
            cursor: pointer;
        }

        .button-group{
            display: flex;
            gap: 15px;
            margin-top: 35px;
        }

        .btn{
            flex: 1;
            text-align: center;
            padding: 15px;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-submit{
            background: white;
            color: #6d28d9;
        }

        .btn-submit:hover{
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn-back{
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .btn-back:hover{
            background: rgba(255,255,255,0.25);
        }

        @media(max-width: 768px){

            body{
                padding: 20px;
            }

            .container{
                padding: 30px;
            }

            .header h1{
                font-size: 28px;
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

        <h1>Tambah Dosen</h1>

        <p>
            Tambahkan data dosen baru ke sistem akademik
        </p>

    </div>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= $_SESSION['csrf_token'] ?>"
        >

        <div class="input-group">

            <label>NIDN</label>

            <input
                type="text"
                name="nidn"
                placeholder="Masukkan NIDN"
                required
            >

        </div>

        <div class="input-group">

            <label>Nama</label>

            <input
                type="text"
                name="nama"
                placeholder="Masukkan nama dosen"
                required
            >

        </div>

        <div class="input-group">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Masukkan email"
                required
            >

        </div>

        <div class="input-group">

            <label>Program Studi</label>

            <select name="program_studi">

                <option value="Teknik Informatika">
                    Teknik Informatika
                </option>

                <option value="Sistem Informasi">
                    Sistem Informasi
                </option>

                <option value="Teknik Elektro">
                    Teknik Elektro
                </option>

                <option value="Manajemen Informatika">
                    Manajemen Informatika
                </option>

                <option value="Ilmu Komputer">
                    Ilmu Komputer
                </option>

                <option value="Teknik Komputer">
                    Teknik Komputer
                </option>

                <option value="Teknologi Informasi">
                    Teknologi Informasi
                </option>

                <option value="Sains Data">
                    Sains Data
                </option>

                <option value="Rekayasa Perangkat Lunak">
                    Rekayasa Perangkat Lunak
                </option>

                <option value="Sistem Komputer">
                    Sistem Komputer
                </option>

            </select>

        </div>

        <div class="input-group">

            <label>Upload Foto</label>

            <input
                type="file"
                name="foto"
                class="file-input"
            >

        </div>

        <div class="input-group">

            <label>Status</label>

            <select name="status">

                <option value="aktif">
                    Aktif
                </option>

                <option value="nonaktif">
                    Nonaktif
                </option>

            </select>

        </div>

        <div class="button-group">

            <a href="index.php" class="btn btn-back">
                Kembali
            </a>

            <button 
                type="submit" 
                class="btn btn-submit"
            >
                Simpan Data
            </button>

        </div>

    </form>

</div>

</body>
</html>