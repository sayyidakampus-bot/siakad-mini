<?php

require_once '../config/database.php';

try {

    $db = Database::connect();

    echo "Koneksi berhasil";

} catch (PDOException $e) {

    echo $e->getMessage();
}