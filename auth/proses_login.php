<?php
session_start();

$conn_path = __DIR__ . '/../helper/connection.php';
require_once $conn_path;

if (!isset($connection)) {
    die("Koneksi database gagal dimuat.");
}

if (isset($_POST['submit'])) {

    $email    = mysqli_real_escape_string($connection, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($connection, "SELECT * FROM admins WHERE email='$email' LIMIT 1");

    if (mysqli_num_rows($query) > 0) {

        $admin = mysqli_fetch_assoc($query);

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin;
            header("Location: ../dashboard/index.php");
            exit;
        } else {
            $_SESSION['error'] = "Password salah!";
        }

    } else {
        $_SESSION['error'] = "Email tidak ditemukan!";
    }

    header("Location: login.php");
    exit;

} else {
    header("Location: login.php");
    exit;
}