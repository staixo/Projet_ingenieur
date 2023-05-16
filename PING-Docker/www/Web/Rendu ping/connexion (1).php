<?php
// On définit un login et un mot de passe de base pour tester notre exemple. Cependant, vous pouvez très bien interroger votre base de données afin de savoir si le visiteur qui se connecte est bien membre de votre site

$connection = 0;

// on teste si nos variables sont définies
if (isset($_POST['login']) && isset($_POST['pwd'])) {
    require './id_bdd.php' ;


    $query = mysqli_query($dbconnect, "SELECT * FROM user;")
    or die (mysqli_error($dbconnect));
    $userMdp=password_hash($_POST['pwd'], PASSWORD_DEFAULT);

    while ($row = mysqli_fetch_array($query)) {

        if($_POST['pwd']==password_verify($_POST['pwd'],$row['password']) && $row['email'] == $_POST['login'])
        {
            $connection = 1;
        }

    }
	// on vérifie les informations du formulaire, à savoir si le pseudo saisi est bien un pseudo autorisé, de même pour le mot de passe
	if ($connection == 1) {
		// dans ce cas, tout est ok, on peut démarrer notre session

		// on la démarre :)
		session_start ();
		// on enregistre les paramètres de notre visiteur comme variables de session ($login et $pwd) (notez bien que l'on utilise pas le $ pour enregistrer ces variables)
		$_SESSION['login'] = $_POST['login'];
        $_SESSION['pwd'] = $userMdp;
        $_SESSION['connect'] = 1;
        $_POST['login'] ="";
        $_POST['pwd'] = "";
		// on redirige notre visiteur vers une page de notre section membre
		header ('location: index.php#1');
	}
	else {
		// Le visiteur n'a pas été reconnu comme étant membre de notre site. On utilise alors un petit javascript lui signalant ce fait
		echo '<body onLoad="alert(\'Membre non reconnu...\')">';
		// puis on le redirige vers la page d'accueil
		echo '<meta http-equiv="refresh" content="0;URL=login.php">';
	}
}
else {
	echo 'Les variables du formulaire ne sont pas déclarées.';
}
?>