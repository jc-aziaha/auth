<?php 
session_start();
?>
<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Bootstrap demo</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        </head>
    <body>

        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="/index.php">Auth</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
                        </li>
                        <?php if( isset($_SESSION['auth']) && !empty($_SESSION['auth']) ) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/logout.php">Déconnexion</a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/register.php">Inscription</a>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </nav>


        <!-- Le contenu spécifique à la page -->
        <main>
            <h1 class="text-center my-3 display-5">Hello <?= isset($_SESSION['auth']) && !empty($_SESSION['auth']) ? htmlspecialchars($_SESSION['auth']['first_name']) : 'World'; ?></h1>
        </main>


        <!-- Le pied de page -->
        <footer>
        <span>Dwwm</span>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>