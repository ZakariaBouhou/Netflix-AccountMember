<?php

try {

    $bdd = new PDO('mysql:host=localhost;dbname=netflix;charset=utf8', 'dbzak', 'Cuicui75012!');
}

catch(Exception $e) {

    die('Erreur : '.$e->getMessage());
}