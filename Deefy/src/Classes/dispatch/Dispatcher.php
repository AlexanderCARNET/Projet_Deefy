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
                    }
                    catch(\Exception $e){
                        //
                    }
                case 'display-playlist':
                    $action = new DisplayPlaylistIdAction();
                    break;
                case 'user-playlist':
                    $action = new DisplaySavePlaylist();
                    break;
                case 'session-playlist':
                    $action = new DisplayPlaylistSession();
                    break;
                default:
                    $action = new DefaultAction();
                    break;
            }

            $html = $action->execute();
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
                <a href="?action=add-user">Inscription</a>';
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
                <a href="?action=playlist">Playlist</a> | 
                <a href="?action=add-playlist">Creer ou ajouter un playlist</a> |    
                <a href="?action=add-track">Ajouter un track</a>    |   
                <a href="?action=display-playlist">Recuperer une playlist par ID</a>    |   
                <a href="?action=user-playlist">Afficher les playlist de User</a>   |
                <a href="?action=session-playlist">Afficher playlist en session</a>
            </nava>
            <main>'.$html.
            '</main>
            <footer><p>&copy; IUT Deefy - 2025</p></footer>
        </body>
        </html>';
        echo $res;
    }
}