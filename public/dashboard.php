<?php

require_once '../src/Auth.php';
require_once '../src/DosenRepository.php';

Auth::guard();

$repository = new DosenRepository();

$programStudiStats = $repository->dashboardStats();

$statusStats = $repository->statusStats();

$totalSKS = $repository->totalSKS();

/* DATA PIE CHART */

$statusLabels = [];
$statusTotals = [];

foreach ($statusStats as $row){

    $statusLabels[] = $row['status'];
    $statusTotals[] = $row['total'];

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            padding: 40px;
            overflow-x: hidden;
        }

        /* BLUR EFFECT */

        body::before{
            content: '';
            position: fixed;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            filter: blur(10px);
            animation: float 6s ease-in-out infinite;
        }

        body::after{
            content: '';
            position: fixed;
            width: 250px;
            height: 250px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            bottom: -80px;
            right: -80px;
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
            position: relative;
            z-index: 2;
        }

        .topbar{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .title h1{
            color: white;
            font-size: 40px;
            font-weight: 700;
        }

        .title p{
            color: rgba(255,255,255,0.8);
            margin-top: 5px;
        }

        .btn-back{
            text-decoration: none;
            background: white;
            color: #4f46e5;
            padding: 12px 20px;
            border-radius: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-back:hover{
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* CARD GRID */

        .cards{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .card{
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 25px;
            padding: 28px;
            color: white;
            transition: 0.3s;
            animation: fadeIn 0.8s ease;
        }

        .card:hover{
            transform: translateY(-5px);
        }

        .card h3{
            font-size: 20px;
            margin-bottom: 22px;
        }

        .card ul{
            list-style: none;
        }

        .card li{
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            font-size: 14px;
        }

        .card li:last-child{
            border-bottom: none;
        }

        /* CHART CARD */

        .chart-card{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 420px;
        }

        .chart-container{
            width: 100%;
            max-width: 320px;
            margin-top: 15px;
        }

        /* BIG CARD */

        .big-card{
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 25px;
            padding: 35px;
            color: white;
            text-align: center;
            animation: fadeIn 1s ease;
        }

        .big-card h2{
            font-size: 24px;
            margin-bottom: 15px;
        }

        .big-number{
            font-size: 70px;
            font-weight: 700;
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

        @media(max-width: 768px){

            body{
                padding: 20px;
            }

            .topbar{
                flex-direction: column;
                align-items: flex-start;
            }

            .title h1{
                font-size: 30px;
            }

            .big-number{
                font-size: 50px;
            }

            .chart-card{
                min-height: auto;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="topbar">

        <div class="title">

            <h1>Dashboard</h1>

            <p>
                Statistik Sistem Informasi Akademik
            </p>

        </div>

        <a href="index.php" class="btn-back">
            Kembali
        </a>

    </div>

    <div class="cards">

        <!-- PROGRAM STUDI -->

        <div class="card">

            <h3>
                Jumlah Dosen per Program Studi
            </h3>

            <ul>

                <?php foreach ($programStudiStats as $row): ?>

                    <li>

                        <span>
                            <?= htmlspecialchars($row['program_studi']) ?>
                        </span>

                        <strong>
                            <?= $row['total'] ?>
                        </strong>

                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

        <!-- PIE CHART -->

        <div class="card chart-card">

            <h3>
                Diagram Status Dosen
            </h3>

            <div class="chart-container">

                <canvas id="statusChart"></canvas>

            </div>

        </div>

    </div>

    <!-- TOTAL SKS -->

    <div class="big-card">

        <h2>
            Total SKS Diampu
        </h2>

        <div class="big-number">

            <?= $totalSKS['total_sks'] ?? 0 ?>

        </div>

    </div>

</div>

<script>

const ctx = document.getElementById('statusChart');

new Chart(ctx, {

    type: 'pie',

    data: {

        labels: <?= json_encode($statusLabels) ?>,

        datasets: [{

            data: <?= json_encode($statusTotals) ?>,

            backgroundColor: [

                '#22c55e',
                '#ef4444'

            ],

            borderWidth: 0

        }]

    },

    options: {

        responsive: true,

        plugins: {

            legend: {

                labels: {

                    color: 'white',
                    font: {
                        size: 14
                    }

                }

            }

        }

    }

});

</script>

</body>
</html>