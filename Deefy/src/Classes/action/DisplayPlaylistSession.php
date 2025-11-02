<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\renderer\AudioListRenderer;

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
        return <<<HTML
            <form method="POST">
                <button type="submit" name="add-track"><a href="?action=add-track">Ajouter Une piste</a></button><br><br>
                <button type="submit" name="delete">Désélectionnée la playlist</button>
            </form>
        HTML;

    }
}