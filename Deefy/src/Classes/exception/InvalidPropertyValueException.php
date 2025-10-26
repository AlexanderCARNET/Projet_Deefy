<?php
namespace iutnc\deefy\exception;
class InvalidPropertyValueException extends \Exception
{

    public function __construct($value)
    {
        parent::__construct($value." : mauvaise valeur de propriete");
    }
}