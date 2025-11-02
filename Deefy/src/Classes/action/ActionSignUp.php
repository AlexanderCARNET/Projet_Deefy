<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\exception\AuthnException;

class ActionSignUp extends Action
{
    public function execute():string{
        if(isset($_SESSION['user'])){
            return "<h2>You are already logged in.</h2>";
        }
        else if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(!empty($_POST['email']) && !empty($_POST['password'])){
                try{
                    Authnprovider::register($_POST['email'], $_POST['password']);
                }
                catch(AuthnException $e){
                    return "<h2>".$e->getMessage()."</h2>".$this->form();
                }
                if(isset($_SESSION['user'])){
                    return "<h2>You are already logged in.</h2>";
                }
                return "<h2>New User, welcome in Deefy, with deefy you can listen all your favorite music!!!</h2>";
            }
        }
        return $this->form();
    }

    public function form():string{
        return <<<HTML
        <h2>Sign up</h2>
        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th><label for="email">Email</label></th>
                        <th><label for="password">Password</label></th>
                    </tr>
                </thead>
                <td>
                    <input type="email" name="email" id="email" required>
                </td>
                <td>
                    <input type="password" name="password" id="password" required>
                </td>
            </table>
            <input type="submit" value="Create account">
        </form>
        HTML;
    }
}