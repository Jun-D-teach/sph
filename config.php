<?php
$DB_HOST='localhost';
$DB_USER='root';     // user MySQL cPanel
$DB_PASS=''; // password
$DB_NAME='sph'; // nama database
$conn=new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($conn->connect_error) die('Gagal koneksi MySQL: '.$conn->connect_error.' — cek config.php & pastikan install.sql sudah diimport.');
$conn->set_charset('utf8mb4');