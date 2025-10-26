<?php

namespace iutnc\deefy\action;

class AddUserAction extends Action
{
    public function execute() : string{
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['age'])){
                $username = $_POST['username'];
                $email = $_POST['email'];
                $age = $_POST['age'];

                $safeUsername = filter_var($username, FILTER_SANITIZE_STRING);
                $safeEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
                $safeAge = filter_var($age, FILTER_SANITIZE_NUMBER_INT);
                $infoUser = ["username" => $safeUsername, "email" => $safeEmail, "age" => $safeAge];
                if(!isset($_SESSION['users'])){
                    $users=[];
                }
                else{
                    $users = $_SESSION['users'];
                }
                $users[]=$infoUser;
                $_SESSION['users'] = $users;

                return "<h2>User created!</h2><italic></itac><li>Username : <strong>{$safeUsername}</strong></li><li>Email : <strong>{$safeEmail}</strong></li><li>Age : <strong>{$safeAge}</strong>ans</li></italic>";
            }
            return '<h2 style="color: red"><strong>Remplir tous les champs!!!</strong></h2>'.$this->form();
        }
        return $this->form();
    }

    public function form() : string{
        return <<<HTML
        <h2>Inscription : </h2>
        <form method="POST">
            <li>
                <label for="email">Adresse mail :</label>
                <input type="text" name="email" id="email">
            </li>
            <li>
                <label for="age">Age :</label>
                <input type="number" name="age" id="age">
            </li>
            <input type="submit" value="Valider">
        </form>
        HTML;
    }
}