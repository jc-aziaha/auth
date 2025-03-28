# Système d'authentification

## La trame à suivre

### 1. Définir la structure de base du site
- Créer toutes les pages requises
    - Accueil
    - Inscription
    - Connexion


### 2. Sur la page d'inscription, 
- Afficher le formulaire correspondant
- Valider le formulaire
- Insérer le nouvel utilisateur en base
- Le Rediriger vers la page de connexion

### 3. Sur la page de connexion, 
- Afficher le formulaire correspondant
- Valider le formulaire
- Authentifier l'utilisateur
- Le Rediriger vers la page d'accueil

### 4. Adapter la barre de navigation
- Si l'utilisateur est connecté,
    - Affichons-lui le lien de déconnexion
- Dans le cas contraire,
    - Affichons-lui le lien de connexion et d'inscription

### 5. Empêcher qu'un utilisateur déjà connecté puisse accéder à la page d'inscription et de connection