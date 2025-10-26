<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\renderer\AudioListRenderer;

class AddPlaylistAction extends Action
{

    public function execute(): string
    {
        if(isset($_SERVER['REQUEST_METHOD'])=="POST"){
            if(!empty($_POST["titre"])){
                echo "hello";
                $titre = $_POST["titre"];
                if(!isset($_SESSION["playlist_{$titre}"])){
                    $playlist = new Playlist($titre, $audios = []);
                    $_SESSION["playlist_{$titre}"] = $playlist;
                }
                else{
                    return '<p><strong>La playlist est deja existent!!!</strong></p>>'.$this->form();
                }
                $render = new AudioListRenderer($playlist);
                $res = $render->render(0);
                return '<p><strong>Cree avec success!!!</strong></p>'.$res.'<a href="?action=add-track">Ajouter une track</a>';
            }
        }
        return $this->form();
    }

    private function form() : string{
        return <<<HTML
        <h2>Ajouter une track dans une playlist</h2>
        <form method="POST">
            <label for="nom">Nom playlist : </label>
            <input type="text" name="titre" id="titre">
            <button type="submit">Cree</button>
        </form>
        HTML ;
    }
}