<?php

namespace iutnc\deefy\repository;

use DateTime;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\auth\Authnprovider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;
use \PDO;
use PDOException;

class DeefyRepository
{
    private static $config;

    private static ?DeefyRepository $instance = null;

    public PDO $db;

    public function __construct(){
        try{
            $dsn = "mysql:host=" . self::$config['host'] . ";dbname=" . self::$config['dbname'];
            $this->db = new PDO($dsn, self::$config['user'], self::$config['password']);
            //echo "Connected successfully!!!";
        }
        catch (PDOException $e){
            echo "Connection failed: " . $e->getMessage();
        }
    }
    public static function setConfig(string $file){
        self::$config = parse_ini_file($file);
    }

    public static function getInstance() : DeefyRepository{
        if(is_null(self::$instance)){
            self::$instance = new DeefyRepository();
        }
        return self::$instance;
    }

    public function recupererPlaylist(int $id):array{
        $sql = $this->db->prepare("SELECT * FROM playlist WHERE id = :id;");
        $sql -> bindParam(':id', $id, PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

    public function saveEmptyPlaylist(Playlist $playlist) : object
    {
        //ajout de la playlist dans la table playlist
        if (is_null($playlist)) {
            throw new \InvalidArgumentException("L'objet playlist est null.");
        }

        $sqlInsert = $this->db->prepare("INSERT INTO playlist (nom) VALUES (:nom)");
        $nomePlaylist = $playlist->nom;
        $sqlInsert->execute([':nom' => $nomePlaylist]);

        $newId = $this->db->lastInsertId();

        $sqlSelect = $this->db->prepare("SELECT * FROM playlist WHERE id = :id");

        $sqlSelect->execute([':id' => $newId]);

        //ajout du lien entre le user et l'id de la playlist
        $prepare = $this->db->prepare("INSERT INTO user2playlist (id_user,id_pl) VALUES (?,?)");
        $prepare->bindValue(1, $_SESSION["user"]["id"]);
        $prepare->bindValue(2, $newId);
        $prepare->execute();


        return $sqlSelect->fetch(PDO::FETCH_OBJ);
    }

    public function savePodcastTrack(PodcastTrack $track): false|string
    {
        if(!is_null($track)){
            $sql = $this->db->prepare("INSERT INTO track (
                titre, 
                genre, 
                duree, 
                filename, 
                type, 
                auteur_podcast, 
                date_posdcast 
            ) VALUES (
                :titre, 
                :genre, 
                :duree, 
                :filename, 
                'P', 
                :auteur_podcast, 
                :date_podcast
            );");
            $params = [
                ':titre'          => $track->__get('titre'),
                ':genre'          => $track->__get('genre'),
                ':duree'          => $track->__get('duree'),
                ':filename'       => $track->__get('nom_fichier_audio'),
                ':auteur_podcast' => $track->__get('auteur_podcast'),
                ':date_podcast'   => $track->__get('date_podcast')->format('Y-m-d')
            ];
            $sql -> execute($params);
        }
        else{
            throw new \Exception("error requete!!!");
        }
        $newId = $this->db->lastInsertId();

        $sqlSelect = $this->db->prepare("SELECT * FROM track WHERE id = :id");

        $sqlSelect->execute([':id' => $newId]);

        //return $sqlSelect->fetch(PDO::FETCH_OBJ);
        return $newId;

    }

    public function addTrackToPlaylist(int $idPlaylist, int $idTrack){
        $sql = $this->db->prepare("select count(*) as nb from track where id = :idTrack;");
        $sql->execute([':idTrack' => $idTrack]);
        $nb = $sql->fetch(PDO::FETCH_ASSOC);
        if($nb['nb'] > 0){
            $sql = $this->db->prepare("select count(*) as nb from playlist where id = :idPlaylist;");
            $sql->execute([':idPlaylist' => $idPlaylist]);
            $nb = $sql->fetch(PDO::FETCH_ASSOC);
            if($nb['nb'] > 0){
                try{
                    $sql = $this->db->prepare("select count(id_pl) as nb from playlist2track where id_pl = :idPlaylist;");
                    $sql->execute([':idPlaylist' => $idPlaylist]);
                    $nb = $sql->fetch(PDO::FETCH_ASSOC);
                    $no = $nb['nb'] + 1;
                    $sql = $this->db->prepare("insert into playlist2track (id_pl, id_track, no_piste_dans_liste) values (:idPlaylist, :idTrack, :no_piste_dans_liste);");
                    $sql->execute([':idPlaylist' => $idPlaylist, ':idTrack' => $idTrack, ':no_piste_dans_liste' => $no]);
                }
                catch (PDOException $e){
                    echo "hello".$e->getMessage();
                }
            }
            else{
                throw new \Exception("error requete(playlist)!!!");
            }
        }
        else{
            throw new \Exception("error requete(track)!!!");
        }
    }

    public function findAllPlaylists():array
    {
        $sql = $this->db->prepare("SELECT * FROM playlist;");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

    public function findUser(string $email)
    :object{

        $sql = $this->db->prepare("SELECT * FROM user WHERE email = :email;");
        $sql->execute([':email' => $email]);
        if($sql->rowCount() > 0)
            return $sql->fetch(PDO::FETCH_OBJ);
        throw new AuthnException("doesn't exist!!!");
    }

    public function getRoleUser(string $email):int{
        $sql = $this->db->prepare("SELECT role FROM user WHERE email = :email;");
        $sql->execute([':email' => $email]);
        if($sql->rowCount() > 0)
            return $sql->fetch(PDO::FETCH_OBJ)->role;
        return 0;
    }

    /**
     * Fonction qui sert à enregistrer l'e-mail et le mot de passe dans la base de données
     *
     * @param string $email
     * @param string $password
     * @return void
     * @throws AuthnException
     */
    public function saveEmailPassword(string $email, string $password){
        $sql = $this->db->prepare("SELECT count(email) as nb FROM user WHERE email = :email;");
        $sql->execute([':email' => $email]);
        $nb = $sql->fetch(PDO::FETCH_ASSOC);
        if($nb['nb'] === 0){
            $sql = $this->db->prepare("insert into user (email, passwd, role) values (:email, :passwd, 1);");
            $sql->execute([':email' => $email, ':passwd' => $password]);
        }
        else{
            throw new AuthnException("Le user il est deja existant!");
        }
    }

    /**
     * Fonction qui retourne une playlist en utilisant uniquement son ID
     *
     * @param int $idPlaylist
     * @return Playlist|null
     * @throws \Exception
     */
    public function findPlayById(int $idPlaylist): ?Playlist{
        $sql = $this->db->prepare("SELECT * FROM playlist p inner join playlist2track pt on pt.id_pl=p.id inner join track t on t.id=pt.id_track WHERE p.id = :idPlaylist;");
        $sql->execute([':idPlaylist' => $idPlaylist]);
        $existe = $this->db->prepare("SELECT id, nom FROM playlist WHERE id = :idPlaylist;");
        $existe->execute([':idPlaylist' => $idPlaylist]);
        if($existe->rowCount() > 0){
            $tracks = [];
            while($allPlaylist = $sql->fetch(PDO::FETCH_OBJ)){
                if($allPlaylist->type === 'P'){
                    $tracks[] = new PodcastTrack($allPlaylist->titre,'Unknown', $allPlaylist->genre, $allPlaylist->duree, new DateTime('01/01/1970'), $allPlaylist->filename, $allPlaylist->auteur_podcast, $allPlaylist->date_posdcast);
                }
                else{
                    $tracks[] = new AlbumTrack($allPlaylist->titre, $allPlaylist->genre, $allPlaylist->duree, new DateTime('01/01/1970'), $allPlaylist->filename, $allPlaylist->artiste_album, $allPlaylist->titre_album, $allPlaylist->annee_album, $allPlaylist->numero_album);
                }
            }
            $playlist = new Playlist($existe->fetch()["nom"], $tracks);
            return $playlist;
        }
        return null;
    }

    /**
     * Fonction qui vérifie si l'utilisateur est le propriétaire de la playlist ou non
     *
     * @param int $id
     * @param int $idPlaylist
     * @return int
     */
    public function checkOwnerShipPlaylist(int $id, int $idPlaylist):int{
        $sql = $this->db->prepare("SELECT u.role FROM user u inner join user2playlist up on up.id_user=u.id WHERE u.id = :id and up.id_pl = :idPlaylist;");
        $sql->execute([':id' => $id, ':idPlaylist' => $idPlaylist]);
        if($sql->rowCount() > 0){
            return $sql->fetch(PDO::FETCH_OBJ)->role;
        }
        return 0;
    }

    /**
     * Fonction qui donne toutes les playlists de l'utilisateur, l'utilisateur sera trouvé avec son email et son identifiant passés en paramètre.
     * Elle renverra un tableau de playlists.
     *
     * @param int $idUser
     * @param string $email
     * @return array|null
     * @throws \Exception
     */
    public function findAllUserPlaylists(int $idUser, string $email):?array{
        $role = Authz::checkRole(Authnprovider::getSignedInUser());
        if($role === 'admin'){
            $sql = $this->db->prepare("select up.id_pl from user2playlist up;");
            $sql->execute();
        }
        else{
            $sql = $this->db->prepare("select up.id_pl from user u inner join user2playlist up on up.id_user=u.id where u.id = :idUser and u.email = :email;");
            $sql->execute([':idUser' => $idUser, ':email' => $email]);
        }

        //Vérifie s'il contient quelque chose à l'intérieur, sinon il retourne null
        if($sql->rowCount() > 0){
            $playlists = [];
            while($allPlaylist = $sql->fetch(PDO::FETCH_OBJ)){
                $playlists[] = $this->findPlayById($allPlaylist->id_pl);
            }
            return $playlists;
        }
        return null;
    }

    /**
     * Fonction qui affiche toutes les playlists avec leurs identifiants
     *
     * @param int $idUser
     * @param string $email
     * @return array|null
     */
    public function findAllUserIdPlaylists(int $idUser, string $email):?array{
        $sql = $this->db->prepare("select up.id_pl, p.nom from user u inner join user2playlist up on up.id_user=u.id inner join playlist p on p.id=up.id_pl where u.id = :idUser and u.email = :email;");
        $sql->execute([':idUser' => $idUser, ':email' => $email]);

        //Vérifie s'il contient quelque chose à l'intérieur, sinon il retourne null
        if($sql->rowCount() > 0){
            return $sql->fetchAll(PDO::FETCH_OBJ);
        }
        return null;
    }

    /**
     * Fonction qui enregistre dans la base de données la playlist, la personne avec qui elle a été partagée et le rôle
     *
     * @param int $idPlaylist
     * @param string $email
     * @param int $role
     * @return void
     * @throws AuthnException
     */
    public function sharePlaylist(int $idPlaylist, string $email, int $role): void
    {
        try{
            $user = $this->findUser($email);
        }catch (AuthnException $ex){
            throw new AuthnException($ex->getMessage());
        }

        $playlist = $this->findPlayById($idPlaylist);

        if ($playlist != null) {
            if ($role === 2 || $role === 3) {
                //verif si on se la partage pas à soit meme
                if(Authnprovider::getSignedInUser()["email"] === $email){
                    throw new AuthnException("Cette playlist vous appartient déjà");
                }

                //verif que l'on est propriétaire de cette playlist
                $sql = $this->db->prepare("select id_user from user2playlist where id_pl = ?");
                $sql->bindValue(1, $idPlaylist);
                $sql->execute();
                if($sql->rowCount() >= 1){
                    $id_createur_pl = $sql->fetch(PDO::FETCH_OBJ);
                    echo $id_createur_pl->id_user ."---". Authnprovider::getSignedInUser()["id"];
                    if($id_createur_pl->id_user != Authnprovider::getSignedInUser()["id"]){
                        throw new AuthnException("Cette playlist ne vous appartient pas !");
                    }
                }


                //insertion
                $sql = $this->db->prepare("INSERT INTO shareplaylist (id_user, id_pl, role) VALUES (:idUser, :idPlaylist, :role);");
                $params = [
                    ':idUser' => $user->id,
                    ':idPlaylist' => $idPlaylist,
                    ':role' => $role
                ];

                try {
                    $sql->execute($params);
                    //echo "Insertion réussie!";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        throw new AuthnException("Cette playlist est déjà partagée avec cet utilisateur.");
                    } else {
                        throw $e;
                    }
                }

            } else {
                throw new \Exception("Rôle non valide");
            }
        } else {
            throw new AuthnException("La playlist n'existe pas.");
        }
    }

    /**
     * Fonction qui permet de savoir quels droits l'utilisateur a sur la playlist partagée
     *
     * @param int $idUser
     * @param string $nomPlaylist
     * @return int
     */
    public function checkSharePermissions(int $idUser, string $nomPlaylist):int{
        $sql = $this->db->prepare("SELECT sp.role FROM sharePlaylist sp inner join playlist p on p.id=sp.id_pl WHERE id_user = :user and p.nom = :nomPlaylist;");
        $sql->execute([':user' => $idUser, ':nomPlaylist' => $nomPlaylist]);
        if($sql->rowCount() > 0){
            return $sql->fetch(PDO::FETCH_OBJ)->role;
        }
        return 0;
    }

    /**
     * Il fonctionne en donnant toutes les playlists partagées avec l'utilisateur passé en paramètre
     *
     * @param int $idUser
     * @param string $email
     * @return array|null
     * @throws \Exception
     */
    public function findAllPlaylistShared(int $idUser, string $email):?array{
        $sql = $this->db->prepare("select sp.id_pl from user u inner join sharePlaylist sp on sp.id_user=u.id where u.id = :idUser and u.email = :email;");
        $sql->execute([':idUser' => $idUser, ':email' => $email]);

        //Vérifie s'il contient quelque chose à l'intérieur, sinon il retourne null
        if($sql->rowCount() > 0){
            $playlists = [];
            while($allPlaylist = $sql->fetch(PDO::FETCH_OBJ)){
                $playlists[] = $this->findPlayById($allPlaylist->id_pl);
            }
            return $playlists;
        }
        return null;
    }
}