<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\repository\DeefyRepository;

class DisplaySavePlaylist extends Action
{
    public function execute():string{
        $user = Authnprovider::getSignedInUser();
        if($user != null){
            $rep = DeefyRepository::getInstance();
            $
        }
    }
}