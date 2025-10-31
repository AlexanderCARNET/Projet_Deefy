<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\PodcastTrack;

class AddPodcastTrackAction extends Action
{
    private string $titre;

    public function __construct(){
        parent::__construct();
    }

    public function execute() : string{
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            if(!empty($_POST["nom"]) && !empty($_FILES["track"]) && !empty($_POST['auteur_podcast']) && !empty($_POST['date_podcast'])){
                echo "hello";

                $this->titre=$_POST["nom"];

                if(isset($_SESSION["playlist_{$this->titre}"])){
                    $playlist=$_SESSION["playlist_{$this->titre}"];
                }
                else{
                    $playlist = new Playlist($this->titre, $temp=[]);
                }

                $path = "./src/classes/audio/";
                move_uploaded_file($_FILES['track']['tmp_name'],$path.$_FILES['track']['name']);
                $pathFile = realpath("./src/classes/audio/".$_FILES['track']['name']);
                echo $pathFile;
                $podcastTrack = new PodcastTrack('', '', '', 0, new \DateTime('01/01/1970'), $pathFile, $_POST['auteur_podcast'], new \DateTime($_POST['date_podcast']));
                $playlist->addAudio($podcastTrack);
                $_SESSION["playlist_{$this->titre}"] = $playlist;
            }
            else{
                return "<p><strong>Invalid propriety!!!!</strong></p>".$this->form();
            }
            return "<p><strong>Track ajoute dans la playlist {$this->titre}</strong></p>";
        }
        return $this->form();
    }

    private function form() : string{
        return <<<HTML
        <h2>Ajouter une PodcastTrack dans une playlist</h2>
        <form enctype="multipart/form-data" method="POST">
            <li>
                <label for="nom">Nom playlist : </label>
                <input type="text" name="nom" id="nom">
            </li>
            <li>
                <label for="titre">Titre track : </label>
                <input type="text" name="titre" id="titre"> |   
                <label for="artiste">Artiste track : </label>
                <input type="text" name="artiste" id="artiste"> |   
                <label for="date">Date track : </label>
                <input type="date" name="date" id="date"> | 
                <label for="genre">Genre track : </label>
                <input type="text" name="genre" id="genre"> |
                <label for="duree">Duree track : </label>
                <input type="number" name="duree" id="duree"> | 
                <label for="fichier">Nom fichier track : </label>
                <input type="text" name="fichier" id="fichier"> |   
                <label for="auteur_podcast">Auteur podcast : </label>
                <input type="text" name="auteur_podcast" id="auteur_podcast"> | 
                <label for="date_podcast">Date podcast : </label>
                <input type="date" name="date_podcast" id="date_podcast">
            </li>
            <label for="track">File track : </label>
            <input type="file" name="track" id="track" accept="mp3">
            <button type="submit">Ajouter</button>
        </form>
        HTML ;
    }
}