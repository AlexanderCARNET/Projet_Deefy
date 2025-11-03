<?php

namespace iutnc\deefy\action;

use iutnc\deefy\renderer\AudioListRenderer;
use iutnc\deefy\renderer\AudioTrackRenderer;

/**
 * Classe qui crée un lecteur et lit la playlist enregistrée en session
 */
class LecteurPlaylist extends Action
{

    /**
     * Fonction qui lit la session, affiche la playlist et le piste/track que nous écoutons en ce moment.
     *
     * @return string
     */
    public function execute():string{
        if(isset($_SESSION['playlist'])){
            $playlist = $_SESSION['playlist'];
            $nb = $playlist->__get('nb_pistes');

            //Il sert à sauvegarder la position de l'audio que nous sommes en train d'écouter
            if(isset($_SESSION['pos'])){
                $pos = $_SESSION['pos'];
            }
            else{
                $_SESSION['pos'] = 0;
                $pos=0;
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if(isset($_POST['suivant'])){
                    $pos++;
                }
                else if(isset($_POST['precedent'])){
                    $pos--;
                }
                if($pos >= $nb){
                    $pos = $nb-1;
                }
                if($pos < 0){
                    $pos = 0;
                }
            }

            $_SESSION['pos']=$pos;
            $piste = $playlist->__get('pistes');
            if(empty($piste)){
                return "<h2>Pas de pistes dans la playlist {$playlist->nom}.</h2>";
            }
            $file = $piste[$pos]->__get('nom_fichier_audio');
            echo $file;
            return "<br>".(new AudioTrackRenderer($piste[$pos]))->render(1).$this->form($file) . (new AudioListRenderer($playlist))->render(0);
        }
        return "<h2>Aucune playlist selectionnee</h2>";
    }

    /**
     * Il fonctionne en affichant les boutons pour changer de chanson et le lecteur audio
     *
     * @param string $file
     * @return string
     */
    public function form(string $file):string{
        if(isset($_SESSION['playlist'])){
            return <<<HTML
                <form method="POST">
                    <input type="submit" name="precedent" value="precedent">
                    <audio controls src="$file"></audio>
                    <input type="submit" name="suivant" value="suivant">
                </form>
            HTML;

        }
        return "<h2>Aucune playlist selectionnee</h2>";
    }
}