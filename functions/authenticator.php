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


    /**
     * Vérifie si un utilisateur est déjà connecté ou non.
     *
     * @param PDO $db
     * @return null|array
     */
    function getUser(PDO $db): null|array
    {
        if ( !isset($_SESSION['auth']) || empty($_SESSION['auth']) ) 
        {
            return null;
        }

        $request = $db->prepare("SELECT * FROM user WHERE id=:id");
        $request->bindValue(":id", $_SESSION['auth']['id']);
        $request->execute();
    
        if ( $request->rowCount() != 1 ) 
        {
            return null;
        }

        return $request->fetch();
    }