<?php
require_once 'config.php'; // Mengimpor file konfigurasi database

$email = $_POST['email'];
$password = $_POST['password'];

if (checkCredentials($email, $password)) {
    // Login berhasil
    $response = array('success' => true);
    echo json_encode($response);
} else {
    // Menampilkan pesan kesalahan
    $response = array('success' => false, 'message' => 'Username or password is incorrect.');
    echo json_encode($response);
}
