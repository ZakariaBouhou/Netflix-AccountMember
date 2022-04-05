<?php

	session_start();

	require_once 'src/option.php';

	if (!empty($_POST['email']) && !empty($_POST['password'])) {

		require_once 'src/bdd.php';

		// On assigne les champs aux variables
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);


		// On vérifie que le mail est bien un mail
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

			header('Location: index.php?error=1&message=Identifiant ou mot de passe incorrect.');
			exit();

		}

		// Le mail saisi existe-il deja en bdd ? 
		$requete = $bdd->prepare('SELECT COUNT(*) as numberEmail FROM user WHERE email = ? ');
		$requete->execute([$email]);

		while ($resultat = $requete->fetch()) {

			if ($resultat['numberEmail'] != 1 ) {

				header('Location: index.php?error=1&message=Identifiant ou mot de passe incorrect.');
				exit();

			}

		}


		// Le mdp saisi correspond à celui en bdd ? 
		$infosUtilisateur = $bdd->prepare('SELECT * FROM user WHERE email = ?');
		$infosUtilisateur->execute([$email]);

		while ($utilisateur = $infosUtilisateur->fetch()) {


			// On vérifie si le mot de passe saisi match avec celui en BDD
			// Si ca match pas, on redirige
			if (!password_verify($password, $utilisateur['password'])) {
				
				header('Location: index.php?error=1&message=Identifiant ou mot de passe incorrect.');
				exit();
				
			}

			else {

				$_SESSION['connect'] = 1;
				$_SESSION['email'] = $utilisateur['email'];

				if (isset($_POST['auto'])) {

					setcookie('auth', $utilisateur['secret'], time() + 365*24*3600, '/', null, false, true);

				}

				header('Location: index.php?success=true');
				exit();

			}

		}

	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">

				<?php if(isset($_SESSION['connect'])) { ?>

					<h1>Bonjour !</h1>
					<?php
					if(isset($_GET['success'])){
						echo'<div class="alert success">Vous êtes maintenant connecté.</div>';
					} ?>
					<p>Qu'allez-vous regarder aujourd'hui ?</p>
					<small><a href="logout.php">Déconnexion</a></small>

				<?php } else { ?>
					<h1>S'identifier</h1>

					<?php if(isset($_GET['error'])) {

						if(isset($_GET['message'])) {
							echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}

					} ?>

					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Votre adresse email" required />
						<input type="password" name="password" placeholder="Mot de passe" required />
						<button type="submit">S'identifier</button>
						<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
					</form>
				

					<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>