<?php

require_once __DIR__ . "/includes/app.php";


use \App\Communication\Email;


$addresses = "alucasvalle@gmail.com";
$subject = "Olá, Lucas!";
$body = "<b>Olá, Lucas!</b><br><br> Você conseguiu enviar email facilmente.";


$obEmail = new Email();


$success = $obEmail->sendEmail($addresses, $subject, $body); 


echo $success ? "Mensagem enviada com sucesso" : $obEmail->getError();