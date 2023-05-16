    <!-- Ce fichier est créé afin de ne pas avoir a repeter le meme code a chaque tentative de connexion a la bdd -->
    <?php 
  $hostname = "mariadb";
  $username = "dalkia";
  $password = "esigelec";
  $db = "bdd";
  $dbconnect=mysqli_connect($hostname,$username,$password,$db);

  if ($dbconnect->connect_error) {
  die("Database connection failed: " . $dbconnect->connect_error);
  }
?>