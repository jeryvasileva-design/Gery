<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'winepainting');

if ($mysqli->connect_error) {
    die('Грешка при свързване: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8');
?>