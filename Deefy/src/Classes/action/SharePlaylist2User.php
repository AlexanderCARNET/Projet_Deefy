<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;
use mysql_xdevapi\Exception;

/**
 * Classe qui sert à partager une playlist avec d'autres utilisateurs et à leur donner des permissions pour la lire et/ou la modifier
 */
class SharePlaylist2User extends Action
{
    /**
     * Fonction qui affiche les playlists de l'utilisateur et vérifie que toutes les données ont été saisies
     *
     * @return string
     */
    public function execute():string{
        $rep = DeefyRepository::getInstance();
        try {
            $user = Authnprovider::getSignedInUser();
        }catch (AuthnException $ex){
            return "<h2>".$ex->getMessage()."</h2>";
        }
        $playlists = $rep->findAllUserIdPlaylists($user['id'], $user['email']);
        $res="<h2>Partage une de tes playlists</h2>";
        if($playlists!=null){
            foreach($playlists as $playlist){
                $res.="<li>".$playlist->id_pl." ".$playlist->nom."</li>";
            }
            if($_SERVER['REQUEST_METHOD'] == "POST"){
                if(!empty($_POST['id_pl']) && !empty($_POST['email']) && !empty($_POST['role'])){
                    try {
                        $rep->sharePlaylist($_POST['id_pl'], $_POST['email'], $_POST['role']);
                    }catch (AuthnException $ex){
                        return "<h2>".$ex->getMessage()."</h2>";
                    }
                }
            }
            return $res.$this->form();
        }
        return "<h2>Vous ne possédez pas de playlists</h2>";
    }

    /**
     * Interface utilisateur
     *
     * @return string
     */
    public function form():string{
        return <<<HTML
            <form method="POST">
                <li>
                    <label for="id_pl">ID playlist:</label>
                    <input type="number" name="id_pl" id="id_pl">
                </li>
                <li>
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email">
                </li>
                <li>
                    <label for="ok">Role:</label>
                    <select name="role">
                        <option value="">--Role--</option>
                        <option value="2">Editor</option>
                        <option value="3">Reader</option>
                    </select>
                </li>
                <button type="submit">Envoyer</button>
            </form>
        HTML;

    }
}