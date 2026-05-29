<?php

require_once '../src/Auth.php';
require_once '../src/DosenRepository.php';

Auth::guard();

$repository = new DosenRepository();

$search = $_GET['search'] ?? '';

$programStudi = $_GET['program_studi'] ?? '';

$status = $_GET['status'] ?? '';

$data = $repository->exportData(
    $search,
    $programStudi,
    $status
);

header('Content-Type: text/csv');

header(
    'Content-Disposition: attachment; filename="dosen.csv"'
);

$output = fopen('php://output', 'w');

fputcsv($output, [
    'NIDN',
    'Nama',
    'Email',
    'Program Studi',
    'Status'
]);

foreach ($data as $row) {

    fputcsv($output, [
        $row['nidn'],
        $row['nama'],
        $row['email'],
        $row['program_studi'],
        $row['status']
    ]);
}

fclose($output);

exit;