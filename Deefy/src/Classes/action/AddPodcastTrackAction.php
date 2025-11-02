<?php

namespace iutnc\deefy\action;

use DateTime;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\repository\DeefyRepository;
use PDO;

class AddPodcastTrackAction extends Action
{
    private string $titre;

    public function __construct(){
        parent::__construct();
    }

    public function execute() : string{
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            if(true){//verifier que tout les champs sont remplies
                //recuperer les données du formulaire
                $titre = filter_var($_POST["titre"],FILTER_SANITIZE_SPECIAL_CHARS);
                $artist = filter_var($_POST["artiste"],FILTER_SANITIZE_SPECIAL_CHARS);
                $date = filter_var($_POST["date"],FILTER_SANITIZE_SPECIAL_CHARS);
                $genre = filter_var($_POST["genre"],FILTER_SANITIZE_SPECIAL_CHARS);
                $duree = filter_var($_POST["duree"],FILTER_SANITIZE_NUMBER_INT);
                $auteur_pod = filter_var($_POST["auteur_pod"],FILTER_SANITIZE_SPECIAL_CHARS);

                //recuperation du fichier audio
                if(!is_uploaded_file($_FILES['monAudio']['tmp_name'])){
                    return "erreur non uploader correctement";
                }
                if(substr($_FILES['monAudio']['name'],-4) !== '.mp3'){
                    return "<p>Mauvaise nom de fichier</p>";
                }
                if($_FILES['monAudio']['type'] !== 'audio/mpeg'){
                    return "<p>Mauvaise type de fichier</p>";
                }

                //enregistrement du fichier audio
                $nouvNom = uniqid("audio_") . ".mp3";
                $chemin = "./src/classes/audio/" . $nouvNom;
                move_uploaded_file($_FILES['monAudio']['tmp_name'],$chemin);

                //creation de track
                $podcastTrack = new PodcastTrack($titre, $artist, $genre, $duree, $date, $chemin, $auteur_pod, $date);
                //ajout dans la playlist qui est en session
                $playlist = $_SESSION['playlist'];
                $playlist->addAudio($podcastTrack);
                $_SESSION['playlist'] = $playlist;
                //enregistrement dans la bd
                //enregistrement du track
                $id_track = DeefyRepository::getInstance()->savePodcastTrack($podcastTrack);
                //recuperation de l'id de la playlist
                $prepare = DeefyRepository::getInstance()->db->prepare("select id from playlist 
                inner join user2playlist on user2playlist.id_pl = playlist.id
                where playlist.nom = ? and user2playlist.id_user = ?");
                $prepare->bindValue(1,$playlist->nom);
                $prepare->bindValue(2,$_SESSION['user']["id"]);
                $prepare->execute();
                $col = $prepare->fetch(PDO::FETCH_ASSOC);
                //enregistrement du track pour la playlist
                DeefyRepository::getInstance()->addTrackToPlaylist($col["id"],$id_track);
            }
            else{
                return "<p><strong>Veuillez rensegnez tout les champs!!!!</strong></p>".$this->form();
            }
            return "<p><strong>Track ajouté dans la playlist {$playlist->nom}</strong></p>";
        }
        return $this->form();
    }

    private function form() : string{
        return <<<HTML
        <h2>Ajouter une PodcastTrack dans une playlist</h2>
        <form enctype="multipart/form-data" method="POST">
            <li>
                <label for="titre">Titre track : </label>
                <input type="text" name="titre" id="titre"> <br>
                <label for="artiste">Artiste track : </label>
                <input type="text" name="artiste" id="artiste"> <br>  
                <label for="date">Date track : </label>
                <input type="date" name="date" id="date" value="01/01/1970"> <br> 
                <label for="genre">Genre track : </label>
                <input type="text" name="genre" id="genre"> <br>
                <label for="duree">Duree track : </label>
                <input type="number" name="duree" id="duree" value="0"  > <br> 
                <label for="genre">Auteur podcast : </label>
                <input type="text" name="auteur_pod" id="auteur_pod"> <br>
            </li>
            <label for="track">File track : </label>
            <input type="file" name="monAudio" id="monAudio" accept="mp3">
            <br>
            <br>
            <button type="submit">Ajouter</button>
        </form>
        HTML ;
    }
}