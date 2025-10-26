<?php
namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack as AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException as InvalidName;


class AudioList{

    protected string $nom;
    protected int $nb_pistes=0;
    protected int $duree_total=0;
    protected array $pistes=[];

    public function __construct(string $name, array $piste){
        $this->nom = $name;
        foreach($piste as $value){
            if($value instanceof AudioTrack){
                $this->pistes[] = $value;
                $this->nb_pistes++;
                $this->duree_total += $value->__get('duree');
            }
        }
    }

    public function __get(string $at): mixed {
        if (property_exists ($this, $at))
            return $this->$at;
        throw new InvalidName ($at);
    }
}