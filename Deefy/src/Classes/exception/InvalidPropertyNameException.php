<?php
namespace iutnc\deefy\exception;
class InvalidPropertyNameException extends \Exception
{

    public function __construct($t)
    {
        parent::__construct($t." : propriete inconnue");
    }
}