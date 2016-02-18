<?php

class Promazavac
{

    public function __construct(){

    }

    /*
     * Zpracuje pozadavek predany v POST
     */
    public function handlePost(){

        switch($_POST['switch']){
            case 'login':
                $user = htmlspecialchars(trim($_POST['user']));
                $password = htmlspecialchars(trim($_POST['password']));
                if($this->auth($user, $password)){
                    $_SESSION['user']=$user;
                    $_SESSION['timeout'] = time();
                }
                break;

            case 'logout':
                unset($_SESSION['user']);
                session_destroy();
                break;

            case 'delete':
                $insec=$_POST['todelete']*3600;
                $deletefrom = date('Y-m-d H:i:s', time()-$insec);
                $query = Databaze::dotaz('DELETE FROM vyhledavani WHERE (Datum < ?)', array($deletefrom));
                if($query == false){
                    echo "ERROR: Nepodařilo se provést průmaz";
                } else {
                    echo "<p>Vyhledávání dřívější než " . $deletefrom . " byla promazána.</p>";
                }
                break;
        }
    }


    /*
     * Vytiskne obsah stranky - prihlaseneho uzivatele a patricne formulare
     */
    public function setContent(){
        if(isset($_SESSION['user'])){
            echo "Přihlášen uživatel " . $_SESSION['user'] . ".";
            $this->addForm("delete");
            $this->addForm("logout");
        } else {
            $this->addForm("login");
        }
    }


    /*
     * Vytiskne pozadovany formular
     */
    private function addForm($form){

        switch ($form){
            case 'delete':
                echo "
            <form method='post' action='/promazavani.php'>
            <p>Chci smazat vyhledávání starší <input type='number' name='todelete' min='0'> hodin.</p>
            <input type='hidden' name='switch' value='delete' />
            <input type='submit' value='Smazat!'/>
        </form> ";
                break;

            case 'login':
                echo"
            <form method='post' action='/promazavani.php'>
            <p>Uživatelské jméno: <input type='text' name='user' value=''/></p>
            <p>Heslo: <input type='password' name='password' value=''/></p>
            <input type='hidden' name='switch' value='login'/>
            <input type='submit' value='Prihlásit!'/>
            </form>";
                break;

            case 'logout':
                echo"
            <form method='post' action=''/promazavani.php'>
                <input type='hidden' name='switch' value='logout' />
                <input type='submit' value='Odhlásit!'/>
            </form>";
                break;
        }
    }


    /*
     * Overi, zda predane jmeno a heslo odpovidaji ulozenemu hashi
     */
    private function auth($user, $password){
        $query = Databaze::dotaz('SELECT password FROM pass WHERE user=?', array($user));
        if($query == false){
            return false;
        }

        $rowCount = $query->fetch(PDO::FETCH_NUM);
        $rows = count($rowCount);
        if ($rows === 1) {
            $hash = $rowCount[0];
            return password_verify($password, $hash);
        }
        else {
            return false;
        }
    }
}