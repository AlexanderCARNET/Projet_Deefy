<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\renderer\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;
use PDO;

class AddPlaylistAction extends Action
{

    public function execute(): string
    {
        //verifier qu'un utilisateur est connecté
        try{
            Authnprovider::getSignedInUser();
        }catch (\Exception){
            return "<h2><strong>Veuillez vous connecter</strong></h2>";
        }

        if(isset($_SERVER['REQUEST_METHOD'])=="POST"){
            if(!empty($_POST["titre"])){
                $titre = filter_var($_POST["titre"], FILTER_SANITIZE_SPECIAL_CHARS);
                //verifier dans la bd que la playlist n'existe pas
                $instance = DeefyRepository::getInstance();
                if(Authz::checkRole(Authnprovider::getSignedInUser())=='admin'){
                    $prepared = $instance->db->prepare("select nom from user2playlist 
                    inner join playlist where nom = ?");
                    $prepared->bindParam(1,$titre);
                }else{
                    $prepared = $instance->db->prepare("select nom from user2playlist 
                inner join playlist on user2playlist.id_pl = playlist.id
                where nom = ? and user2playlist.id_user = ?");
                    $prepared->bindParam(1,$titre);
                    $prepared->bindParam(2,$_SESSION["user"]["id"]);
                }
                $prepared->execute();
                if(!$prepared->fetch()){
                    //elle n'existe pas, on la crée
                    $playlist = new Playlist($titre, $audios = []);
                }
                else{
                    //elle existe donc on informe l'utilisateur
                    return '<p><strong>La playlist est deja existent!!!</strong></p>>'.$this->form();
                }
                //enregistrer dans la base de données
                DeefyRepository::getInstance()->saveEmptyPlaylist($playlist);

                //enregistrer en playlist courante dans la session
                $_SESSION["playlist"] = $playlist;

                //affichage
                $render = new AudioListRenderer($playlist);
                $res = $render->render(0);
                return '<p><strong>Créée avec success!!!</strong></p>'.$res.'<a href="?action=add-track">Ajouter une track</a>';
            }
        }
        return $this->form();
    }

    private function form() : string{
        return <<<HTML
        <h2>Ajouter un track dans une playlist</h2>
        <form method="POST">
            <label for="nom">Nom playlist : </label>
            <input type="text" name="titre" id="titre">
            <button type="submit">Créer</button>
        </form>
        HTML ;
    }
}