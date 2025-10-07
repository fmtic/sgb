<?php
$host = 'localhost';
$db   = 'sgb';
$user = 'sgb';
$pass = '36230000';
$charset = 'utf8';

$dsn = "pgsql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
