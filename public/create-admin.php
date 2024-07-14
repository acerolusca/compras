<?php

$host = "127.0.0.1";
$dbName = "ppgcc";
$userName= "root";
$password = "Batatafrita0132@";


$pdo = new PDO("mysql:host=$host;dbname=$dbName", $userName, $password);




$newPass = password_hash("Batatafrita0132@.", PASSWORD_ARGON2ID);

$sql = "INSERT INTO administrator (firstName, lastName, email, password, registerDate) VALUES ('Antonio', 'Lucas da Silva vale', 'alucasvalle@gmail.com', '" . $newPass . "', '" .  (new DateTime())->format("Y-m-d H:i:s") . "')";

$pdo->exec($sql);


