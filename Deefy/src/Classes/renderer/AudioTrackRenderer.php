<?php

namespace iutnc\deefy\renderer;
use iutnc\deefy\renderer\Renderer as Renderer;
class AudioTrackRenderer implements Renderer{

    protected object $track;

    public function __construct(object $audioTrack){
        $this->track = $audioTrack;
    }

    public function render(int $selector): mixed {
        switch($selector){
            case 1:
                return $this->renderCompact();
            case 2:
                return $this->renderLong();
            default:
                return (string)$this->track;
        }
    }

    private function renderCompact(): string {
        return "<div class='audio-track compact'>
                    <strong>{$this->track->titre}</strong> - {$this->track->artiste}
                </div>";
    }

    private function renderLong(): string {
        return "<div class='audio-track long'>
                    <h2>{$this->track->titre}</h2>
                    <ul>
                        <li><strong>Artista:</strong> {$this->track->artiste}</li>
                        <li><strong>Genere:</strong> {$this->track->genre}</li>
                        <li><strong>Durata:</strong> {$this->formatDuration($this->track->duree)}</li>
                        <li><strong>Data:</strong> {$this->track->date}</li>
                        <li><strong>File:</strong> {$this->track->nom_fichier_audio}</li>
                    </ul>
                </div>";
    }
}