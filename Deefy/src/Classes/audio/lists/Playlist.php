<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class Playlist extends AudioList{

    public function __construct(string $name, array $audios){
        parent::__construct($name, $audios);
    }

    public function addAudio(AudioTrack $track): void
    {
        $lists = $this->__get('pistes');
        $n = count($lists);
        $lists[$n]=$track;
        $this->pistes= $lists;
        $this->updateInfo();
    }

    public function delAudio(int $index):void{
        $lists = $this->__get('pistes');
        unset($lists[$index]);
        $this->pistes = $lists;
        $this->updateInfo();
    }

    public function addAudioList(AudioList $list):void{
        $lists = $this->__get('pistes');
        $lists2 = $list->__get('pistes');
        foreach ($lists2 as $track){
            $i=0;
            foreach ($lists as $track2){
                if($track->__get('titre') === $track2->__get('titre') && $track->__get('artiste') === $track2->__get('artiste')){
                    $i++;
                }
            }
            if($i==0){
                $n=count($lists);
                $lists[$n]=$track;
            }
        }
        $this->pistes = $lists;
        $this->updateInfo();
    }

    private function updateInfo():void{
        $this->nb_pistes=0;
        $this->duree_total=0;
        foreach($this->pistes as $value){
            if($value instanceof AudioTrack){
                $this->nb_pistes++;
                $this->duree_total += $value->__get('duree');
            }
        }
    }
}