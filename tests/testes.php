<?php


$now = new DateTime();

$birthDate = new DateTime("2002-01-25");


echo $birthDate->diff($now)->y;









