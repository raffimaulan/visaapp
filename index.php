<?php
session_start();

if (isset($_SESSION['admin'])) {
    header('Location: dashboard/index.php');
    exit;
} else {
    header('Location: auth/login.php');
    exit;
}