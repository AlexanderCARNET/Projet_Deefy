<?php

namespace iutnc\deefy\action;

use iutnc\deefy\renderer\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;


class DisplayPlaylistAction extends Action
{

    /**
     * @throws \Exception
     */
    public function execute() : string{
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(!empty($_POST["titre"])){
                $titre = $_POST["titre"];
                if(isset($_SESSION["playlist_{$titre}"])){
                    $playlist = $_SESSION["playlist_{$titre}"];
                    $renderer = new AudioListRenderer($playlist);
                    return $renderer->render(0);
                }
                else {
                    return "<p><strong>La playlist n'est pas existant!!!</strong></p>".$this->form();
                }
            }
            return "<p><strong>Invalid title!!!</strong></p>".$this->form();
        }
        return $this->form();
    }

    public function form() : string{
        return <<<HTML
        <h2>Afficher une playlist</h2>
        <form method="POST">
            <label for="nom">Nom playlist : </label>
            <input type="text" name="titre" id="titre"> |   
            <button type="submit">Afficher</button> 
        </form>
HTML;

    }
}