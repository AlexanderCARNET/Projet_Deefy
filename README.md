# Projet_Deefy

BOUAOUKEL Walid
CARNET Alexander


Information comptes utilisateurs : 

    email            mot de passe

user1@mail.com           user1

user2@mail.com           user2

user3@mail.com           user3

admin@mail.com           admin



!!!
Sur webetu la création de compte utilisateur ne fonctionne pas. La requete SQL de la class DeefyRepository, à la ligne 187. La requete renvoit une ligne avec le mail de l'utilisateur que l'on veux creer alors qu'il n'existe pas dans la base de données.
Cependant sur un server local la fonctionnalité marche correctement et cette même requete ne renvoit aucune ligne.
!!!