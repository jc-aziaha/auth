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

                        <form method="post">
                            <div class="mb-3">
                                <input type="text" name="firstName" class="form-control" placeholder="Votre prénom" autofocus>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="lastName" class="form-control" placeholder="Votre nom">
                            </div>
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Votre email">
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Votre mot de passe">
                            </div>
                            <div class="mb-3">
                                <input type="password" name="confirmPassword" class="form-control" placeholder="Confirmation du met de passe">
                            </div>
                            <div>
                                <input type="submit" class="btn btn-primary w-100" value="S'inscrire">
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p>Vous avez déjà un compte? <a href="">Connectez-vous</a></p>
                            <p>Retour à l'accueil</p>
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