<?php

require_once '../src/Auth.php';
require_once '../src/DosenRepository.php';

Auth::guard();
Auth::adminOnly();

$repository = new DosenRepository();

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID tidak ditemukan");
}

$repository->restore($id);
$repository->logActivity(
    $_SESSION['user_id'],
    'restore',
    'dosen',
    $id,
    'Restore dosen'
);

header("Location: trash.php");
exit;