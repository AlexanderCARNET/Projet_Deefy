<?php
namespace iutnc\deefy\audio\lists;

use Cassandra\Date;
use iutnc\deefy\audio\lists\AudioList as AudioList;
use iutnc\deefy\exception\InvalidPropertyNameException as InvalidName;

class Albums extends AudioList
{

    private string $artiste;

    private date $date_sortie;

    public function __construct(string $name, string $artiste, date $date_sortie, array $piste)
    {
        parent::__construct($name, $piste);
        $this->artiste = $artiste;
        $this->date_sortie = $date_sortie;
    }


    public function __get($at) : mixed{
        if (property_exists($this, $at))
            return $this->$at;
        throw new InvalidName($at);
    }
}