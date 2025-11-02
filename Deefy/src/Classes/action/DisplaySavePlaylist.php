<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

/**
 * Cette classe sert à afficher les playlists d'un utilisateur sauvegardées
 * dans la base de données et ensuite à sélectionner la playlist que l'on souhaite
 * enregistrer en session afin de l'utiliser ultérieurement.
 */
class DisplaySavePlaylist extends Action
{

    /**
     * fonction execute qui sert à sauvegarder dans la session la liste de lecture choisie par l'utilisateur
     *
     * @return string
     * @throws \Exception
     */
    public function execute():string{
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            //je vérifie si l'utilisateur s'est connecté ou non
            try {
                $user = Authnprovider::getSignedInUser();
            }catch (AuthnException $ex){
                return "<h2>".$ex->getMessage()."</h2>";
            }
            $rep = DeefyRepository::getInstance();
            $playlists = $rep->findAllUserPlaylists($user['id'], $user['email']);
            if(!empty($playlists)){
                $res="<h2>User Playlist</h2><nava>";
                foreach($playlists as $playlist){
                    if(!empty($_POST['playlist']))
                        if($playlist->__get('nom') == $_POST['playlist']){
                            $_SESSION['playlist'] = $playlist;
                            $_SESSION['pos'] = 0;
                            return $this->form()."<h2>Playlist selected!!</h2>";
                        }
                }
            }
            $playlists = $rep->findAllPlaylistShared($user['id'], $user['email']);
            if(!empty($playlists)){
                $res="<h2>User shared Playlist</h2><nava>";
                foreach($playlists as $playlist){
                    if(!empty($_POST['playlist']))
                        if($playlist->__get('nom') == $_POST['playlist']){
                            $_SESSION['playlist'] = $playlist;
                            return $this->form()."<h2>Playlist selected!!</h2>";
                        }
                }
            }
        }
        return $this->form();
    }

    /**
     * fonction pour l'interface
     *
     * @return string
     * @throws \Exception
     */
    public function form():string{
        try {
            $user = Authnprovider::getSignedInUser();
        }catch (AuthnException $ex){
            return "<h2>".$ex->getMessage()."</h2>";
        }
        $rep = DeefyRepository::getInstance();
        $playlists = $rep->findAllUserPlaylists($user['id'], $user['email']);
        $res="<h2>User Playlist</h2><form method='post'>";
        if(empty($playlists)){
            $res.="<h2>Vous ne possédez pas encore de Playlists</h2>";
        }
        else {
            foreach ($playlists as $playlist) {
                $res .= "<li><button type='submit' name='playlist' value='" . $playlist->__get('nom') . "'>" . $playlist->__get('nom') . "</button></li>";
            }
        }

        $playlists = $rep->findAllPlaylistShared($user['id'], $user['email']);
        $res.='<h2>User shared Playlist</h2>';
        if(empty($playlists)){
            $res.="<p>Vous ne possédez pas encore de Playlists partagees avec vous</p>";
        }
        else{
            foreach($playlists as $playlist){
                $res.= "<li><button type='submit' name='playlist' value='".$playlist->__get('nom')."'>".$playlist->__get('nom')."</button></li>";
            }
        }
        return $res."</form>";
    }
}