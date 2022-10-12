# Méthodo : validation Signup

1. modifier l'entité User
   - ajouter la propriété $signUpToken
   - créer un ROLE_TEMPORARY

2. Créer un générateur de $signUpToken dans UserController

3. modifier la méthode adduser du UserController
    - pour setter le $signUpToken
    - envoyer un mail via un Service
    - setter le role : ["ROLE_TEMPORARY"] (qui deviendra ["ROLE_REFEREE"] après la validation de l'email)

4. envoyer l'email avec dans le corps du mail une url/methode confirmAccount/{$signUpToken}

5. Créer la méthode confirmAccount
    - retrouve le User via le $signUpToken
    - si User est retrouvé
      - set $signUpToken à null (ou "validate" ?)
      - set le role ["ROLE_REFEREE"]
      - redirection vers page confirmation inscription + lien vers acceuil
    - si User non retrouvé redirection vers erreur + lien vers acceuil

6. se pencher sur la configuration d'un envoie d'email **smtp.gmail.com**