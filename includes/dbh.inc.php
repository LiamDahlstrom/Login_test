<?php
$dBServername = "127.0.0.1:8112";
$dBUsername = "root";
$dBPassword = "";
$dBName = "login_db";// byt till namnet på er databas i PHPmyadmin

// Create connection
$conn = mysqli_connect($dBServername, $dBUsername, $dBPassword, $dBName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}