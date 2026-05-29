<?php

require_once '../src/Auth.php';
require_once '../src/DosenRepository.php';

Auth::guard();
Auth::adminOnly();

$repository = new DosenRepository();

$dosen = $repository->trash();

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Trash Dosen</title>

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
            overflow-x: hidden;
            position: relative;
            padding: 30px;
        }

        body::before{
            content: '';
            position: fixed;
            width: 350px;
            height: 350px;
            background: rgba(255,255,255,0.10);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            filter: blur(10px);
        }

        body::after{
            content: '';
            position: fixed;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.10);
            border-radius: 50%;
            bottom: -100px;
            right: -100px;
            filter: blur(10px);
        }

        /* TOGGLE */

        .menu-toggle{
            position: fixed;
            top: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1001;
            color: white;
            font-size: 26px;
            transition: 0.3s;
        }

        .menu-toggle:hover{
            background: rgba(255,255,255,0.25);
        }

        /* SIDEBAR */

        .sidebar{
            position: fixed;
            top: 0;
            left: -280px;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #4338ca, #6d28d9, #7c3aed);
            padding: 90px 20px 30px;
            z-index: 1000;
            transition: 0.4s ease;
            box-shadow: 5px 0 25px rgba(0,0,0,0.2);
        }

        .sidebar.active{
            left: 0;
        }

        .logo{
            color: white;
            margin-bottom: 40px;
        }

        .logo h2{
            font-size: 30px;
            margin-bottom: 5px;
        }

        .logo p{
            font-size: 13px;
            color: rgba(255,255,255,0.7);
        }

        .sidebar-menu{
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .sidebar-menu a{
            text-decoration: none;
            color: white;
            padding: 14px 18px;
            border-radius: 14px;
            background: rgba(255,255,255,0.10);
            transition: 0.3s;
            font-size: 15px;
            font-weight: 500;
        }

        .sidebar-menu a:hover{
            background: rgba(255,255,255,0.20);
            transform: translateX(5px);
        }

        .logout{
            background: rgba(239,68,68,0.2) !important;
        }

        /* CONTENT */

        .container{
            transition: 0.4s;
        }

        .container.shift{
            margin-left: 280px;
        }

        .topbar{
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .title h1{
            color: white;
            font-size: 38px;
            margin-bottom: 8px;
        }

        .title p{
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }

        .card{
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 28px;
            padding: 30px;
            color: white;
            overflow-x: auto;
        }

        .back-btn{
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            background: white;
            color: #6d28d9;
            padding: 14px 20px;
            border-radius: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .back-btn:hover{
            transform: translateY(-2px);
        }

        .table-wrapper{
            overflow-x: auto;
        }

        table{
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            min-width: 700px;
        }

        table th{
            background: rgba(255,255,255,0.18);
            padding: 18px 16px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            color: white;
        }

        table td{
            padding: 18px 16px;
            background: rgba(255,255,255,0.05);
            text-align: center;
        }

        table tr td:first-child{
            border-top-left-radius: 14px;
            border-bottom-left-radius: 14px;
        }

        table tr td:last-child{
            border-top-right-radius: 14px;
            border-bottom-right-radius: 14px;
        }

        table tr:hover td{
            background: rgba(255,255,255,0.08);
        }

        .restore-btn{
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 12px;
            background: rgba(34,197,94,0.2);
            color: #bbf7d0;
            font-weight: 600;
            transition: 0.3s;
            display: inline-block;
        }

        .restore-btn:hover{
            transform: translateY(-2px);
        }

        @media(max-width: 768px){

            body{
                padding: 20px;
            }

            .container.shift{
                margin-left: 0;
            }

            table{
                min-width: 600px;
            }

        }

    </style>

</head>

<body>

<!-- TOGGLE -->

<div class="menu-toggle" onclick="toggleSidebar()">
    ☰
</div>

<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">

    <div class="logo">

        <h2>DosenKu</h2>

        <p>
            Sistem Informasi Data Dosen
        </p>

    </div>

    <div class="sidebar-menu">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <a href="index.php">
            📚 Data Dosen
        </a>

        <a href="export.php">
            📁 Export CSV
        </a>

        <a href="logout.php" class="logout">
            🚪 Logout
        </a>

    </div>

</div>

<!-- CONTENT -->

<div class="container" id="container">

    <div class="topbar">

        <div class="title">

            <h1>Trash Dosen</h1>

            <p>
                Data dosen yang sudah dihapus
            </p>

        </div>

    </div>

    <div class="card">

        <a href="index.php" class="back-btn">
             Kembali ke Data Dosen
        </a>

        <div class="table-wrapper">

            <table>

                <tr>

                    <th>NIDN</th>
                    <th>Nama</th>
                    <th>Aksi</th>

                </tr>

                <?php foreach ($dosen as $row): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($row['nidn']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama']) ?>
                        </td>

                        <td>

                            <a
                                href="restore.php?id=<?= $row['id'] ?>"
                                class="restore-btn"
                            >
                                Restore
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        </div>

    </div>

</div>

<script>

function toggleSidebar(){

    document.getElementById('sidebar').classList.toggle('active');

    document.getElementById('container').classList.toggle('shift');

}

</script>

</body>
</html>