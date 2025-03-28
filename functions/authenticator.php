<?php


    /**
     * Vérifie si l'email envoyé par l'utilisateur existe dans la base de données ou non.
     *
     * @param string $email
     * @param PDO $db
     * 
     * @return boolean retourne true si l'email existe déjà et false dans le cas contraire.
     */
    function already_exists(string $email, PDO $db): bool
    {
        $request = $db->prepare("SELECT * FROM user WHERE email=:email");
        $request->bindValue(":email", $email);
        $request->execute();

        if ( $request->rowCount() == 1 ) 
        {
            return true;
        }
        
        return false;
    }