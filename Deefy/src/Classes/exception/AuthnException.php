<?php
namespace iutnc\deefy\exception;
class AuthnException extends \Exception
{

    public function __construct(string $t)
    {
        parent::__construct("Author error: ".$t);
    }
}
