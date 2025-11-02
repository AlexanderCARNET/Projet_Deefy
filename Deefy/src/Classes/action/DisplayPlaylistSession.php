<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\renderer\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

/**
 * Classe qui affiche la playlist enregistrée dans une session
 */
class DisplayPlaylistSession extends Action
{
    /**
     * Fonction qui affiche dans l'interface utilisateur la playlist enregistrée dans la session
     *
     * @return string
     */
    public function execute():string{
        if(isset($_SESSION['playlist'])){
            $playlist = $_SESSION['playlist'];
            $render = new AudioListRenderer($playlist);
            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                //Je vérifie si le bouton a été appuyé
                if(isset($_POST['delete'])){
                    unset($_SESSION['playlist']);
                    unset($_SESSION['pos']);
                    return "<h2>Playlist Désélectionnée</h2>";
                }
            }
            return $render->render(0).$this->form();
        }
        return "<h2>Aucune playlist enregistrée</h2>";
    }

    /**
     * Fonction pour ajouter un bouton pour supprimer la playlist de la session sur l'interface
     *
     * @return string
     */
    public function form():string{
        $res = '
            <form method="POST">';
        $rep = DeefyRepository::getInstance();
        try {
            $user = Authnprovider::getSignedInUser();
        }catch (AuthnException $ex){
            return "<h2>".$ex->getMessage()."</h2>";
        }
        //Je vérifie si j'ai les permissions pour la modifier ou non
        if($rep->checkSharePermissions($user['id'], $_SESSION['playlist']->__get('nom')) == 2 || $rep->checkSharePermissions($user['id'], $_SESSION['playlist']->__get('nom')) == 0){
            $res.='<button type="submit" name="add-track"><a href="?action=add-track">Ajouter Une piste</a></button><br><br>';
        }
        return $res.'<button type="submit" name="delete">Désélectionnée la playlist</button></form>';
    }
}