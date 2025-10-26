<?php

namespace iutnc\deefy\renderer;

use iutnc\deefy\audio\tracks\PodcastTrack as PodcastTrack;
class PodcastRenderer extends AudioTrackRenderer{
        public function __construct(PodcastTrack $a)
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

        public function renderCompact(): string
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
                    <p>Artiste : {$this->track->artiste}</p>
                    <p>date : ({$this->track->date})</p>
                    <p>Genre : {$this->track->genre}</p>
                    <p>Durée : {$this->track->duree} secondes</p>
                    <audio controls src='{$this->track->nom_fichier_audio}'></audio>
                </div>";
        }
    }