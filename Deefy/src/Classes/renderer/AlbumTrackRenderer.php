<?php

namespace iutnc\deefy\renderer;

use iutnc\deefy\audio\tracks\AlbumTrack as AlbumTrack;
use iutnc\deefy\renderer\AudioTrackRenderer as AudioTrackRenderer;

class AlbumTrackRenderer extends AudioTrackRenderer
{

    public function __construct(AlbumTrack $a)
    {
        parent::__construct($a);
    }

    public function render(int $selector): mixed
    {
        switch ($selector) {
            case 1:
                return $this->renderCompact();
            case 2:
                return $this->renderLong();
            default:
                return $this->track->__toString();
        }
    }

    private function renderCompact(): string
    {
        return "<div class='track compact'>
                    <strong>{$this->track->titre}</strong> - {$this->track->artiste}
                    <audio controls src='{$this->track->nom_fichier_audio}'></audio>
                </div>";
    }

    private function renderLong(): string
    {
        return "<div class='track long'>
                    <h2>{$this->track->titre}</h2>
                    <ul>
                        <li><strong>Artista:</strong> {$this->track->artiste}</li>
                        <li><strong>Album:</strong> {$this->track->album} ({$this->track->annee})</li>
                        <li><strong>Traccia:</strong> {$this->track->nb_piste}</li>
                        <li><strong>Genere:</strong> {$this->track->genre}</li>
                        <li><strong>Durata:</strong> {$this->track->duree}</li>
                    </ul>
                    <audio controls src='{$this->track->nom_fichier_audio}'></audio>
                </div>";
    }
}