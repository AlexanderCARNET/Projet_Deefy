<?php

namespace iutnc\deefy\auth;

use http\Client\Curl\User;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class Authnprovider
{

    public static function signin(string $email, string $password):void{
        $repo = DeefyRepository::getInstance();
        try {
            $user = $repo->findUser($email);
        }catch (AuthnException $ex){
            throw new AuthnException($ex->getMessage());
        }
        if ($user && password_verify($password, $user->passwd)) {
            $_SESSION['user'] = [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ];
            return;
        } else {
            throw new AuthnException("Email ou password pas valide.");
        }
    }

    public static function register(string $email, string $password):void{
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new AuthnException("invalid email.");
        }
        if(Authnprovider::checkPasswordStrength($password)){
            $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        }
        else{
            throw new AuthnException("Author error: invalid password.");
        }
        $repo=DeefyRepository::getInstance();
        $repo->saveEmailPassword($email, $hash);
        try {
            $user = $repo->findUser($email);
        }catch (AuthnException $ex){
            throw new AuthnException($ex->getMessage());
        }
        $_SESSION['user'] = [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ];
        return;
    }

    private static function checkPasswordStrength(string $password):bool{
        $length = (strlen($password) < 10);
        $digit = preg_match("/[\d]/", $password);
        $special = preg_match("#[\W]#", $password);
        $lower = preg_match("#[a-z]#", $password);
        $upper = preg_match("#[A-Z]#", $password);
        return (!$length && $digit && $special && $lower && $upper);
    }

    /**
     * fonction de déconnexion qui déconnecte l'utilisateur et supprime la playlist enregistrée
     * @return void
     */
    public static function logout():void{
        unset($_SESSION['user']);
        if(isset($_SESSION['playlist'])){
            unset($_SESSION['playlist']);
        }
        if(isset($_SESSION['pos'])){
            unset($_SESSION['pos']);
        }
    }

    public static function getSignedInUser():mixed{
        if(!isset($_SESSION['user'])){
            throw new AuthnException("You are not logged in!");
        }
        return $_SESSION['user'];
    }
}