<?php

	session_start();

	require_once 'src/option.php';

	if (isset($_SESSION['connect'])) {
		
		header('Location: index.php');
		exit();

	}

	if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])) {


		require_once 'src/bdd.php';

		// On assigne les champs aux variables
		$email = htmlspecialchars($_POST['email']);
		$firstPassword = htmlspecialchars($_POST['password']);
		$secondPassword = htmlspecialchars($_POST['password_two']);


		// On vérifie que les mdp soient bien identiques
		if ($firstPassword !== $secondPassword) {

			header('Location: inscription.php?error=1&message=Les mots de passe ne sont pas identiques');
			exit();


		}

		// On vérifie que le mail est bien un mail
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

			header('Location: inscription.php?error=1&message=Email ou mot de passe incorrect');
			exit();

		}

		// On vérifie que l'email saisi n'existe pas deja en BDD
		$requete = $bdd->prepare('SELECT COUNT(*) as numberEmail FROM user WHERE email = ?');
		$requete->execute([$email]);

		while ($resultat = $requete->fetch()) {

			if ($resultat['numberEmail'] != 0) {

				header('Location: inscription.php?error=1&message=L\'email saisi est déja utilisé');
				exit();

			}

		}

		$secret = uniqid();

		// On insert l'utilisateur en BDD
		$insert = $bdd->prepare('INSERT INTO user (email, password, secret) VALUES (:email, :password, :secret)');
		$insert->execute([

			'email' 	=> $email,
			'password' 	=> password_hash($firstPassword, PASSWORD_DEFAULT),
			'secret' 	=> $secret,

		]);

		
		header('Location: inscription.php?success=1');
		exit();

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
			<h1>S'inscrire</h1>

			<form method="post" action="inscription.php">
				<?php if(isset($_GET['error']) ) {

					if(isset($_GET['message'])) {
						echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
					}

				} ?>
				<?php if(isset($_GET['success']) ) { ?>

					<div class="alert success">Vous êtes bien inscrit. <a href="index.php">Connectez-vous</a></div>

				<?php } ?>
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>