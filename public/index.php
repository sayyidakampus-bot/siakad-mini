<?php

require_once '../src/Auth.php';
require_once '../src/DosenRepository.php';

Auth::guard();

$repository = new DosenRepository();

$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;

$limit = 10;
$offset = ($page - 1) * $limit;

$programStudi = $_GET['program_studi'] ?? '';
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'nama';
$direction = $_GET['direction'] ?? 'ASC';

$totalData = $repository->countData(
    $search,
    $programStudi,
    $status
);

$totalPages = ceil($totalData / $limit);

$dosen = $repository->all(
    $search,
    $programStudi,
    $status,
    $sort,
    $direction,
    $limit,
    $offset
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Dosen</title>

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

        .add-btn{
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

        .add-btn:hover{
            transform: translateY(-2px);
        }

        .filter-form{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .filter-form input,
        .filter-form select{
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: none;
            outline: none;
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .filter-form input::placeholder{
            color: rgba(255,255,255,0.7);
        }

        .filter-form select option{
            color: black;
        }

        .filter-btn{
            border: none;
            border-radius: 14px;
            background: white;
            color: #6d28d9;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .filter-btn:hover{
            transform: translateY(-2px);
        }

        .table-wrapper{
            overflow-x: auto;
        }

        table{
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            min-width: 1100px;
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

        .foto{
            width: 70px;
            height: 70px;
            border-radius: 16px;
            object-fit: cover;
        }

        .badge{
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .aktif{
            background: rgba(34,197,94,0.2);
            color: #bbf7d0;
        }

        .nonaktif{
            background: rgba(239,68,68,0.2);
            color: #fecaca;
        }

        .action-group{
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .action-btn{
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            transition: 0.3s;
            color: white;
        }

        .edit-btn{
            background: rgba(255,255,255,0.18);
        }

        .delete-btn{
            background: rgba(239,68,68,0.2);
            color: #fecaca;
        }

        .pagination{
            margin-top: 35px;
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .pagination a{
            text-decoration: none;
            min-width: 45px;
            height: 45px;
            padding: 0 15px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255,255,255,0.18);
            color: white;
            font-weight: 600;
        }

        .active-page{
            background: white !important;
            color: #6d28d9 !important;
        }

        @media(max-width: 768px){

            body{
                padding: 20px;
            }

            .container.shift{
                margin-left: 0;
            }

            table{
                min-width: 900px;
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

        <h2>SIAKAD</h2>

        <p>
            Sistem Informasi Manajemen Dosen & Mata Kuliah
        </p>

    </div>

    <div class="sidebar-menu">

        <a href="dashboard.php">
            🏠 Dashboard
        </a>

        <?php if (Auth::isAdmin()): ?>

            <a href="trash.php">
                🗑️ Trash
            </a>

        <?php endif; ?>

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

            <h1>Data Dosen</h1>

            <p>
                Selamat datang,
                <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>
            </p>

        </div>

    </div>

    <div class="card">

        <?php if (Auth::isAdmin()): ?>

            <a href="create.php" class="add-btn">
                + Tambah Dosen
            </a>

        <?php endif; ?>

        <form method="GET" class="filter-form">

            <input
                type="text"
                name="search"
                placeholder="Cari nama / NIDN"
                value="<?= htmlspecialchars($search) ?>"
            >

            <select name="program_studi">

    <option value="">
        Semua Prodi
    </option>

    <option value="Teknik Informatika"
        <?= $programStudi == 'Teknik Informatika' ? 'selected' : '' ?>>
        Teknik Informatika
    </option>

    <option value="Sistem Informasi"
        <?= $programStudi == 'Sistem Informasi' ? 'selected' : '' ?>>
        Sistem Informasi
    </option>

    <option value="Teknik Elektro"
        <?= $programStudi == 'Teknik Elektro' ? 'selected' : '' ?>>
        Teknik Elektro
    </option>

    <option value="Manajemen Informatika"
        <?= $programStudi == 'Manajemen Informatika' ? 'selected' : '' ?>>
        Manajemen Informatika
    </option>

    <option value="Ilmu Komputer"
        <?= $programStudi == 'Ilmu Komputer' ? 'selected' : '' ?>>
        Ilmu Komputer
    </option>

    <option value="Teknik Komputer"
        <?= $programStudi == 'Teknik Komputer' ? 'selected' : '' ?>>
        Teknik Komputer
    </option>

    <option value="Sistem Komputer"
        <?= $programStudi == 'Sistem Komputer' ? 'selected' : '' ?>>
        Sistem Komputer
    </option>

    <option value="Sains Data"
        <?= $programStudi == 'Sains Data' ? 'selected' : '' ?>>
        Sains Data
    </option>

    <option value="Rekayasa Perangkat Lunak"
        <?= $programStudi == 'Rekayasa Perangkat Lunak' ? 'selected' : '' ?>>
        Rekayasa Perangkat Lunak
    </option>

    <option value="Teknologi Informasi"
        <?= $programStudi == 'Teknologi Informasi' ? 'selected' : '' ?>>
        Teknologi Informasi
    </option>

</select>

            <select name="status">

    <option value="">
        Semua Status
    </option>

    <option value="aktif"
        <?= $status == 'aktif' ? 'selected' : '' ?>>
        Aktif
    </option>

    <option value="nonaktif"
        <?= $status == 'nonaktif' ? 'selected' : '' ?>>
        Nonaktif
    </option>

</select>

            <select name="direction">

    <option value="ASC"
        <?= $direction == 'ASC' ? 'selected' : '' ?>>
        Nama A-Z
    </option>

    <option value="DESC"
        <?= $direction == 'DESC' ? 'selected' : '' ?>>
        Nama Z-A
    </option>

</select>

            <button type="submit" class="filter-btn">
                Filter
            </button>

        </form>

        <div class="table-wrapper">

            <table>

                <tr>

                    <th>Foto</th>
                    <th>NIDN</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Program Studi</th>
                    <th>Mata Kuliah</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

                <?php foreach ($dosen as $row): ?>

                    <tr>

                        <td>

                            <?php if ($row['foto']): ?>

                                <img
                                    src="../uploads/<?= htmlspecialchars($row['foto'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="foto"
                                >

                            <?php else: ?>

                                Tidak ada foto

                            <?php endif; ?>

                        </td>

                        <td>
                            <?= htmlspecialchars($row['nidn'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['program_studi'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['mata_kuliah_list'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>

                            <span class="badge <?= $row['status'] ?>">

                                <?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?>

                            </span>

                        </td>

                        <td>

                            <div class="action-group">

                                <a
                                    href="edit.php?id=<?= $row['id'] ?>"
                                    class="action-btn edit-btn"
                                >
                                    Edit
                                </a>

                                <?php if (Auth::isAdmin()): ?>

                                    <a
                                        href="delete.php?id=<?= $row['id'] ?>"
                                        class="action-btn delete-btn"
                                    >
                                        Delete
                                    </a>

                                <?php endif; ?>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        </div>

        <div class="pagination">

    <?php
        $queryString = $_GET;
    ?>

    <!-- PREV -->
    <?php if ($page > 1): ?>
        <?php
            $queryString['page'] = $page - 1;
        ?>
        <a href="?<?= http_build_query($queryString) ?>">
            Prev
        </a>
    <?php endif; ?>

    <!-- NUMBER -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

        <?php
            $queryString['page'] = $i;
        ?>

        <a
            href="?<?= http_build_query($queryString) ?>"
            class="<?= $page == $i ? 'active-page' : '' ?>"
        >
            <?= $i ?>
        </a>

    <?php endfor; ?>

    <!-- NEXT -->
    <?php if ($page < $totalPages): ?>
        <?php
            $queryString['page'] = $page + 1;
        ?>
        <a href="?<?= http_build_query($queryString) ?>">
            Next
        </a>
    <?php endif; ?>

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