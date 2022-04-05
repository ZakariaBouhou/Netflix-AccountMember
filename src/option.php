<?php

// Créer une connexion automatique apres connexion d'un utilisateur
// Si y'a un cookie et qu'un utilisateur n'est pas deja connecté
if (isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {

    // Connexion à la BDD
    require_once 'bdd.php';

    // On initialise une variable $secret dans laquelle on lui assigne la valeur du cookie auth
    $secret = htmlspecialchars($_COOKIE['auth']);

    // Vérifications
    // On selectionne le nombre de secret sur la ligne secret données
    $requete = $bdd->prepare('SELECT COUNT(*) AS secretNumber FROM user WHERE secret = ? ');
    $requete->execute([$secret]);


    // On parcours chaque ligne de la table user 
    while ($userInformations = $requete->fetch()) {

        // Si n'y a qu'un secret, alors on connecte l'user en créant la session utilisateur
        if ($userInformations['secretNumber'] == 1) {
            
            // On récupère toutes les informations de l'utilisateur
            $infosUser = $bdd->prepare('SELECT * FROM user WHERE secret = ? ');
            $infosUser->execute([$secret]);

            while ($infos = $infosUser->fetch()) {

                //var_dump($infos);

                $_SESSION['connect'] = 1;
                $_SESSION['email'] = $infos['email'];

            }


        }

    }
    
     

}