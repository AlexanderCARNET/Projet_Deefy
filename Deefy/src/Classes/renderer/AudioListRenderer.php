<?php

namespace iutnc\deefy\renderer;

use iutnc\deefy\audio\lists\Albums as Albums;
use iutnc\deefy\audio\lists\AudioList as AudioList;
use iutnc\deefy\audio\lists\Playlist as Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\renderer\Renderer as Renderer;

class AudioListRenderer implements Renderer{

    private AudioList $track;

    public function __construct(AudioList $audioTrack){
        $this->track = $audioTrack;
    }
    
    public function render(int $t): string{
        $i=0;
        $res="<div class = 'audio'>
                <style>th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }</style>
                <h2>{$this->track->__get('nom')}</h2>
                <p>N° pistes : {$this->track->__get('nb_pistes')}</p>
                <p>Duree total : {$this->track->__get('duree_total')}</p>
                <table><thead><th>id</th><th>titre</th><th>artiste</th><th>annee</th><th>duree</th><th>genre</th><th>filename</th><th>type</th><th>artiste_album</th><th>titre_album</th><th>annee_album</th><th>numero_album</th><th>auteur_podcast</th><th>date_podcast</th></tr></thead>";
        foreach($this->track->__get('pistes') as $piste) {
            if ($piste instanceof AlbumTrack) {
                $res = $res . "<tr><td>{$i}</td><td>{$piste->__get('titre')}</td><td>{$piste->__get('artiste')}</td><td>{$piste->__get('date')->format('d/m/Y')}</td><td>{$piste->__get('genre')}</td><td>{$piste->__get('duree')}</td><td>{$piste->__get('nom_fichier_audio')}</td><td>A</td><td>{$piste->__get('artiste_album')}</td><td>{$piste->__get('album')}</td><td>{$piste->__get('annee')}</td><td>{$piste->__get('nb_piste')}</td><td>null</td><td>null</td></tr>";
            } else if ($piste instanceof PodcastTrack) {
                $res = $res . "<tr><td>{$i}</td><td>{$piste->__get('titre')}</td><td>{$piste->__get('artiste')}</td><td>{$piste->__get('date')->format('d/m/Y')}</td><td>{$piste->__get('genre')}</td><td>{$piste->__get('duree')}</td><td>{$piste->__get('nom_fichier_audio')}</td><td>P</td><td>null</td><td>null</td><td>null</td><td>null</td><td>{$piste->__get('auteur_podcast')}</td><td>{$piste->__get('date_podcast')->format('d/m/y')}</tr>";
            }
            $i++;
        }
        return ($res."</table></div>");
    }
}