<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\ActionSignIn;
use iutnc\deefy\action\ActionSignUp;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
//use iutnc\deefy\action\AddUserAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\DisplayPlaylistIdAction;
use iutnc\deefy\action\DisplayPlaylistSession;
use iutnc\deefy\action\DisplaySavePlaylist;
use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\action\LecteurPlaylist;

class Dispatcher
{
    private string $action;

    public function __construct(){
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run() : void {
        $html = "";

        //if($this->titre!="null"){
            switch($this->action){
                case 'add-user':
                //    $action = new AddUserAction();
                    $action = new ActionSignUp();
                    break;
                case 'signin':
                    $action = new ActionSignIn();
                    break;
                case 'playlist':
                    $action = new DisplayPlaylistAction();
                    break;
                case 'add-playlist':
                    $action = new AddPlaylistAction();
                    break;
                case 'add-track':
                    $action = new AddPodcastTrackAction();
                    break;
                case 'logOut':
                    try{
                        Authnprovider::logout();
                        $action = new DefaultAction();
                    }
                    catch(\Exception $e){
                        //
                    }
                    break;
                case 'display-playlist':
                    $action = new DisplayPlaylistIdAction();
                    break;
                case 'user-playlist':
                    $action = new DisplaySavePlaylist();
                    break;
                case 'session-playlist':
                    $action = new DisplayPlaylistSession();
                    break;
                case 'lecteur-playlist':
                    $action = new LecteurPlaylist();
                    break;
                default:
                    $action = new DefaultAction();
                    break;
            }
            try{
                $html = $action->execute();
            }catch (\Exception){
                $this->renderPage("<p>Une erreur est survenue veuillez recommencer </p>");
            }

        //}
        $this->renderPage($html);
    }

    public function renderPage(string $html) : void {
        $res = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Deefy - Application</title>
        </head>
        <body>
            <nav>
                <a href="?action=add-user">Inscription</a><u> | </u>';
        if(isset($_SESSION['user'])){
            $res .= '<a href="?action=logOut">Log out</a>';
        }
        else{
            $res .= '<a href="?action=signin">Sign in</a>';
        }
        $res .= '</nav>
            <header><h1>Deefy - Application</h1></header>
            <nava>
                <a href="?action=default">Accueil</a> | 
                <a href="?action=add-playlist">Créer une playlist</a> |
                <a href="?action=user-playlist">Mes playlists</a>|
                <a href="?action=session-playlist">Afficher playlist selectionnée</a>
                <a href="?action=lecteur-playlist">Lecteur playlist</a>
            </nava>
            <main>'.$html.
            '</main>
            <footer><p>&copy; IUT Deefy - 2025</p></footer>
        </body>
        </html>';
        echo $res;
    }
}