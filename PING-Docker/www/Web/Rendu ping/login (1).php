
<?php
// On démarre la session
session_start ();

// On détruit les variables de notre session
session_unset ();

// On détruit notre session
session_destroy ();
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Prediction pour le suivi de client </title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body class="login">
    <div>
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <form action="connexion.php" method="post">Dalkia Prediction Clients
                        <h1>Se connecter</h1>
                        <div>
                            <input type="text" class="form-control" name="login" placeholder="E-mail" required="" />
                        </div>
                        <div>
                            <input type="password" class="form-control" name="pwd" placeholder="Mot de passe" required="" />
                        </div>
                        <div>
                        <input class="btn btn-default submit" type="submit" value="Se connecter">
                            <a class="reset_pass" href="#">Mot de passe oublié ?</a>
                        </div>

                        <div class="clearfix"></div>

                        <div class="separator">
                            <!--<p class="change_link">New to site?
                                <a href="#signup" class="to_register"> Create Account </a>
                            </p>-->

                            <div class="clearfix"></div>
                            <br />

                            <div>
                            <h1><img id="dalkia" src=".\images\logo_final.png"></h1>
                                <p>©2019 All Rights Reserved to Dalkia. Privacy and Terms</p>
                            </div>
                        </div>
                    </form>
                </section>
            </div>

            <div id="register" class="animate form registration_form">
                <section class="login_content">
                    <form>
                        <h1>Create Account</h1>
                        <div>
                            <input type="text" class="form-control" placeholder="Username" required="" />
                        </div>
                        <div>
                            <input type="email" class="form-control" placeholder="Email" required="" />
                        </div>
                        <div>
                            <input type="password" class="form-control" placeholder="Password" required="" />
                        </div>
                        <div>
                            <a class="btn btn-default submit" href="login.php">Submit</a>
                        </div>

                        <div class="clearfix"></div>

                        <div class="separator">
                            <p class="change_link">Already a member ?
                                <a href="#signin" class="to_register"> Log in </a>
                            </p>

                            <div class="clearfix"></div>
                            <br />

                            <div>
                            <h1><img id="dalkia" src=".\images\logo_final.png"></h1>
                                <p>©2019 All Rights Reserved. Privacy and Terms</p>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
    <style>
    #dalkia{
        width : 100px;
        
    }
    </style>
</body>

</html>