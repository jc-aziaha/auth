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

        // var_dump($_POST); die();

        // 1. Protéger le serveur contre les failles de type csrf
        if ( ! array_key_exists('csrf_token', $_POST) ) 
        {
            return header("Location: register.php");
        }

        if ( !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ) 
        {
            return header("Location: register.php");
        }

        if ( empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) )
        {
            return header("Location: register.php");
        }
        
        if ( $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) 
        {
            return header("Location: register.php");
        }

        
        // 2. Protéger le serveur contre les robots spameurs
        if ( ! array_key_exists('honey_pot', $_POST) ) 
        {
            return header("Location: register.php");
        }
        
        if ( $_POST['honey_pot'] !== "" ) 
        {
            return header("Location: register.php");
        }

        
        // var_dump("Continuer la partie"); die();
        // 3. Définir les contraintes de validation
        $formErrors = [];

        if ( isset($_POST['firstName']) ) 
        {
            if ( trim($_POST['firstName']) == "" ) 
            {
                $formErrors['firstName'] = "Le prénom est obligatoire.";
            }
            else if( mb_strlen($_POST['firstName']) > 255 )
            {
                $formErrors['firstName'] = "Le prénom ne doit pas dépasser 255 caractères.";
            }
            else if( ! preg_match("/^[0-9A-Za-zÀ-ÖØ-öø-ÿ' -]+$/u", $_POST['firstName']) )
            {
                $formErrors['firstName'] = "Le prénom ne peut contenir que lettres, des chiffres, le tiret du milieu.";
            }
        }

        if ( isset($_POST['lastName']) ) 
        {
            if ( trim($_POST['lastName']) == "" ) 
            {
                $formErrors['lastName'] = "Le nom est obligatoire.";
            }
            else if( mb_strlen($_POST['lastName']) > 255 )
            {
                $formErrors['lastName'] = "Le nom ne doit pas dépasser 255 caractères.";
            }
            else if( ! preg_match("/^[0-9A-Za-zÀ-ÖØ-öø-ÿ' -]+$/u", $_POST['lastName']) )
            {
                $formErrors['lastName'] = "Le nom ne peut contenir que lettres, des chiffres, le tiret du milieu.";
            }
        }

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
            else if( already_exists($_POST['email'], $db) )
            {
                $formErrors['email'] = "Impossible de créer un compte avec cet email.";
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


        if ( isset($_POST['confirmPassword']) ) 
        {
            if ( trim($_POST['confirmPassword']) == "" ) 
            {
                $formErrors['confirmPassword'] = "La confirmation du mot de passe est obligatoire.";
            }
            else if ( mb_strlen($_POST['confirmPassword']) < 12 ) 
            {
                $formErrors['confirmPassword'] = "La confirmation du mot de passe doit contenir au minimum 12 caractères.";
            }
            else if ( mb_strlen($_POST['confirmPassword']) > 255 ) 
            {
                $formErrors['confirmPassword'] = "La confirmation du mot de passe doit contenir au maximum 255 caractères.";
            }
            else if( ! preg_match("/^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ỳ])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ỳ0-9]).{11,255}$/", $_POST['password']) )
            {
                $formErrors['confirmPassword'] = "La confirmation du mot de passe doit contenir au moins un chiffre, une lettre majuscule et minuscule, un caractère spécial.";
            }
            else if ( $_POST['confirmPassword'] !== $_POST['password'] ) 
            {
                $formErrors['confirmPassword'] = "Le mot de passe doit être identique à sa confirmation.";
            }
        }

        // 4. Si le formulaire est soumis et invalide
        if ( count($formErrors) > 0 ) 
        {

            $_SESSION['formErrors'] = $formErrors;

            $_SESSION['old'] = $_POST;

            // Effectuer une redirection vers la page de laquelle proviennent les données
            // Arrêter l'exécution du script.
            return header("Location: register.php");
        }        
        
        // Dans le cas contraire,

        // 5. Encodons d'abord le mot de passe
        $passwordHashed = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // 6. Effectuer la requête d'insertion du nouvel utilisateur en base
        try 
        {
            $request = $db->prepare("INSERT INTO user (first_name, last_name, email, password, created_at, updated_at) VALUES (:first_name, :last_name, :email, :password, now(), now() ) ");
    
            $request->bindValue(":first_name", $_POST['firstName']);
            $request->bindValue(":last_name", $_POST['lastName']);
            $request->bindValue(":email", $_POST['email']);
            $request->bindValue(":password", $passwordHashed);
    
            $request->execute();
            $request->closeCursor();
        } 
        catch (\PDOException $exception) 
        {
            throw new Exception($exception->getMessage());
        }

        // 7. Générer le message flash de succès
        $_SESSION['success'] = "Votre compte a bien été créé, vous pouvez vous connecter.";

        // 8. Rediriger l'utilisateur vers la page de connexion
        // Arrêter l'exécution du script.
        return header("Location: login.php");
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
                        <h1 class="text-center my-3 display-5">Inscription</h1>

                        <?php if(isset($_SESSION['formErrors']) && !empty($_SESSION['formErrors'])) : ?>
                            <div class="alert alert-danger" role="alert">
                                <ul>
                                    <ul>
                                        <?php foreach($_SESSION['formErrors'] as $error) : ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                </ul>
                            </div>
                            <?php unset($_SESSION['formErrors']); ?>
                        <?php endif ?>


                        <form method="post">
                            <div class="mb-3">
                                <input type="text" name="firstName" class="form-control" placeholder="Votre prénom" autofocus value="<?= isset($_SESSION['old']['firstName']) && !empty($_SESSION['old']['firstName']) ? htmlspecialchars($_SESSION['old']['firstName']) : ''; unset($_SESSION['old']['firstName']); ?>">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="lastName" class="form-control" placeholder="Votre nom" value="<?= isset($_SESSION['old']['lastName']) && !empty($_SESSION['old']['lastName']) ? htmlspecialchars($_SESSION['old']['lastName']) : ''; unset($_SESSION['old']['lastName']); ?>">
                            </div>
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Votre email" value="<?= isset($_SESSION['old']['email']) && !empty($_SESSION['old']['email']) ? htmlspecialchars($_SESSION['old']['email']) : ''; unset($_SESSION['old']['email']); ?>">
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Votre mot de passe" value="<?= isset($_SESSION['old']['password']) && !empty($_SESSION['old']['password']) ? htmlspecialchars($_SESSION['old']['password']) : ''; unset($_SESSION['old']['password']); ?>">
                                <small><em>Le mot de passe doit contenir au moins un chiffre, une lettre majuscule et minuscule, un caractère spécial.</em></small>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="confirmPassword" class="form-control" placeholder="Confirmation du mot de passe" value="<?= isset($_SESSION['old']['confirmPassword']) && !empty($_SESSION['old']['confirmPassword']) ? htmlspecialchars($_SESSION['old']['confirmPassword']) : ''; unset($_SESSION['old']['confirmPassword']); ?>">
                            </div>
                            <div>
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            </div>
                            <div>
                                <input type="hidden" name="honey_pot" value="">
                            </div>
                            <div>
                                <input formnovalidate type="submit" class="btn btn-primary w-100" value="S'inscrire">
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p>Vous avez déjà un compte? <a href="">Connectez-vous</a></p>
                            <p><a href="/index.php">Retour à l'accueil</a></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <!-- Image -->
                        <img class="img-fluid" src="/assets/images/register.png" alt="Image invitant à vous inscrire">
                    </div>
                </div>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>