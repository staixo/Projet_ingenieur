<html>
<head>
<title>Formulaire d'identification</title>
</head>

<body>
<form action="test1.php" method="post">
Votre login : <input type="text" name="login">
<br />
Votre mot de passé : <input type="password" name="pwd"><br />
<input type="submit" value="Connexion">
</form>

</body>
</html>
<?php
// On démarre la session
session_start ();

// On détruit les variables de notre session
session_unset ();

// On détruit notre session
session_destroy ();

// On redirige le visiteur vers la page d'accueil
header ('location: Web/Rendu ping/index.php');
?>