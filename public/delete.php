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

$repository->softDelete($id);
$repository->logActivity(
    $_SESSION['user_id'],
    'delete',
    'dosen',
    $id,
    'Soft delete dosen'
);

header("Location: index.php");
exit;