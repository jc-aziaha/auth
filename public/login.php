<?php
session_start();

    require __DIR__ . "/../functions/dbConnector.php";
    require __DIR__ . "/../functions/authenticator.php";

    $db = connectToDb();

    if ( getUser($db) ) 
    {
        return header("Location: index.php");
    }

    // Si les données arrivent au serveur via la métthode POST
    if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
    {

        /**
         * *********************************************
         * Traitement des données du formulaire
         * *********************************************
         */

        // 1. Protéger le serveur contre les failles de type csrf
        if ( ! array_key_exists('csrf_token', $_POST) ) 
        {
            return header("Location: login.php");
        }

        if ( !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ) 
        {
            return header("Location: login.php");
        }

        if ( empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) )
        {
            return header("Location: login.php");
        }
        
        if ( $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) 
        {
            return header("Location: login.php");
        }

        
        // 2. Protéger le serveur contre les robots spameurs
        if ( ! array_key_exists('honey_pot', $_POST) ) 
        {
            return header("Location: login.php");
        }
        
        if ( $_POST['honey_pot'] !== "" ) 
        {
            return header("Location: login.php");
        }

        
        // var_dump("Continuer la partie"); die();
        // 3. Définir les contraintes de validation
        $formErrors = [];

        if ( isset($_POST['email']) ) 
        {
            if ( trim($_POST['email']) == "" ) 
            {
                $formErrors['email'] = "L'email est obligatoire.";
            }
            else if( mb_strlen($_POST['email']) > 255 )
            {
                $formErrors['email'] = "L'email ne doit pas dépasser 255 caractères.";
            }
            else if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) )
            {
                $formErrors['email'] = "L'email est invalide.";
            }
        }

        if ( isset($_POST['password']) ) 
        {
            if ( trim($_POST['password']) == "" ) 
            {
                $formErrors['password'] = "Le mot de passe est obligatoire.";
            }
            else if ( mb_strlen($_POST['password']) < 12 ) 
            {
                $formErrors['password'] = "Le mot de passe doit contenir au minimum 12 caractères.";
            }
            else if ( mb_strlen($_POST['password']) > 255 ) 
            {
                $formErrors['password'] = "Le mot de passe doit contenir au maximum 255 caractères.";
            }
            else if( ! preg_match("/^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ỳ])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ỳ0-9]).{11,255}$/", $_POST['password']) )
            {
                $formErrors['password'] = "Le mot de passe doit contenir au moins un chiffre, une lettre majuscule et minuscule, un caractère spécial.";
            }

        }

        // 4. Si le formulaire est soumis et invalide
        if ( count($formErrors) > 0 ) 
        {

            $_SESSION['formErrors'] = $formErrors;

            $_SESSION['old'] = $_POST;

            // Effectuer une redirection vers la page de laquelle proviennent les données
            // Arrêter l'exécution du script.
            return header("Location: login.php");
        }        
        
        // Dans le cas contraire,
        
        // 5. Vérifions si l'email envoyé par l'utilisateur correspond à celui d'un utilisateur de la base de données

        $request = $db->prepare("SELECT * FROM user WHERE email=:email");

        $request->bindValue(":email", $_POST['email']);

        $request->execute();
        $rowCount = $request->rowCount();
                    
        if ( $rowCount != 1 ) 
        {
            $_SESSION['badCredentials'] = "Les identifiants sont invalides.";
            $_SESSION['old'] = $_POST;
            return header("Location: login.php");
        }

        $user = $request->fetch();

        if ( ! password_verify($_POST['password'], $user['password']) ) 
        {
            $_SESSION['badCredentials'] = "Les identifiants sont invalides.";
            $_SESSION['old'] = $_POST;
            return header("Location: login.php");
        }

        $_SESSION['auth'] = $user;

        // Rediriger l'utilisateur vers la page d'accueil
        // Arrêter l'exécution du script.
        return header("Location: index.php");
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(10));
?>
<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Bootstrap demo</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        </head>
    <body class="bg-light">

        <!-- Le contenu spécifique à la page -->
        <main>
            <div class="container my-5">
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Form -->
                        <h1 class="text-center my-3 display-5">Connexion</h1>

                        <?php if(isset($_SESSION['formErrors']) && !empty($_SESSION['formErrors'])) : ?>
                            <div class="alert alert-danger" role="alert">
                                <ul>
                                    <?php foreach($_SESSION['formErrors'] as $error) : ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                            <?php unset($_SESSION['formErrors']); ?>
                        <?php endif ?>


                        <?php if(isset($_SESSION['badCredentials']) && !empty($_SESSION['badCredentials'])) : ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?= $_SESSION['badCredentials'] ?>
                            </div>
                            <?php unset($_SESSION['badCredentials']); ?>
                        <?php endif ?>


                        <form method="post">
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Votre email" value="<?= isset($_SESSION['old']['email']) && !empty($_SESSION['old']['email']) ? htmlspecialchars($_SESSION['old']['email']) : ''; unset($_SESSION['old']['email']); ?>">
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Votre mot de passe" value="<?= isset($_SESSION['old']['password']) && !empty($_SESSION['old']['password']) ? htmlspecialchars($_SESSION['old']['password']) : ''; unset($_SESSION['old']['password']); ?>">
                                <small><em>Le mot de passe doit contenir au moins un chiffre, une lettre majuscule et minuscule, un caractère spécial.</em></small>
                            </div>
                            <div>
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            </div>
                            <div>
                                <input type="hidden" name="honey_pot" value="">
                            </div>
                            <div>
                                <input formnovalidate type="submit" class="btn btn-primary w-100" value="Se connecter">
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p>Vous n'avez pas encore de compte? <a href="">Inscrivez-vous</a></p>
                            <p><a href="/index.php">Retour à l'accueil</a></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <!-- Image -->
                        <img class="img-fluid" src="/assets/images/login.png" alt="Image invitant à vous connecter">
                    </div>
                </div>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>