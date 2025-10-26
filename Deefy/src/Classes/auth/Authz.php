<?php

namespace iutnc\deefy\auth;

use http\Client\Curl\User;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class Authz
{
    private static $role;
    public static function checkRole(array $user):string{
        $repo=DeefyRepository::getInstance();
        if(is_null(self::$role)){
            throw new AuthnException("Error role not set!!!");
        }
        if($user != null){
            foreach (self::$role as $key => $value){
                if($repo->getRoleUser($user['email'])==$key){
                    return $value;
                }
            }
        }
        return false;
    }

    public static function checkPlaylistOwnership(array $user, int $id):bool{
        $repo=DeefyRepository::getInstance();
        if(is_null(self::$role)){
            throw new AuthnException("Error role not set!!!");
        }
        if($user != null){
            if(self::checkRole($user)=='admin' || self::checkRole($user)=='ADMIN'){
                return true;
            }
            if($repo->checkOwnerShipPlaylist($user['id'],$id)!==0)
                if(self::$role[$repo->checkOwnerShipPlaylist($user['id'],$id)]==self::checkRole($user)){
                    return true;
                }
        }
        return false;
    }

    public static function setConfig(string $config):void{
        self::$role = parse_ini_file($config);
    }
}