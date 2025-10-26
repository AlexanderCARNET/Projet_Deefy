<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\renderer\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;


class DisplayPlaylistIdAction extends Action
{

    public function execute() : string{
        try{
            $user = Authnprovider::getSignedInUser();
        }catch(AuthnException $e){
            return "<h2>".$e->getMessage()."</h2>".$this->form();
        }
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(!empty($_POST["id"])){
                $id = $_POST["id"];
                $rep = DeefyRepository::getInstance();
                $playlist = $rep->findPlayById($id);
                if($playlist != null){
                    if(Authz::checkPlaylistOwnership($user, $id)){
                        $renderer = new AudioListRenderer($playlist);
                        return $renderer->render(0);
                    }
                    else{
                        return "<h2><strong>Tu n'as pas l'acces pour cette playlist!!!</strong></h2>".$this->form();
                    }
                }
                else{
                    return "<h2><strong>La playlist n'est pas existant!!!</strong></h2>".$this->form();
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
            <label for="id">ID playlist : </label>
            <input type="number" name="id" id="id"> |   
            <button type="submit">Afficher</button> 
        </form>
HTML;

    }
}