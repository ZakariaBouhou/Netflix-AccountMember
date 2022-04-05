<?php

session_start(); // Initalisation de la session
session_unset(); // Désactivation de la session
session_destroy(); // Destruction

setcookie('auth', '', time() - 1); // On détruit le cookie

header('Location: index.php');
