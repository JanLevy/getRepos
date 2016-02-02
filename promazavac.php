<?php
session_start();
include 'library.php';

/*
 * Overi, zda predane jmeno a heslo odpovidaji ulozenemu hashi
 */
function auth ($user, $password){
    $sql = "SELECT password FROM pass WHERE user=" . $user;
    $query = dbQuery($sql);

    if(!$query['query']){
        mysqli_close($query['link']);
        return false;
    }

    if (mysqli_num_rows($query['query']) === 1) {
        $row = mysqli_fetch_assoc($query['query']);
        $hash = $row["password"];
        mysqli_close($query['link']);
        return password_verify($password, $hash);
        }
    else {
        return false;
    }
}

/*
 * Zpracuje pozadavek predany v POST
 */
function handlePost(){

    switch($_POST['switch']){
        case 'login':
            $user = htmlspecialchars(trim($_POST['user']));
            $password = htmlspecialchars(trim($_POST['password']));
            if(auth($user, $password)){
                $_SESSION['user']=$user;
            }
            break;

        case 'logout':
            unset($_SESSION['user']);
            session_destroy();
            break;

        case 'delete':
            $insec=$_POST['todelete']*3600;
            $deletefrom = date('Y-m-d H:i:s', time()-$insec);
            $sql = "DELETE FROM vyhledavani WHERE (Datum <'" . $deletefrom . "')";
            $query = dbQuery($sql);
            if(!$query['query']){
                echo "ERROR: Nepodařilo se provést $sql. " . mysqli_error($query['link']);
            } else {
                echo "<p>Vyhledávání dřívější než " . $deletefrom . " byla promazána.</p>";
            }
            mysqli_close($query['link']);
            break;
    }
}

/*
 * Vytiskne pozadovany formular
 */
function addForm($form){

    switch ($form){
        case 'delete':
            echo "
            <form method='post' action='/promazavac.php'>
            <p>Chci smazat vyhledávání starší <input type='number' name='todelete' min='0'> hodin.</p>
            <input type='hidden' name='switch' value='delete' />
            <input type='submit' value='Smazat!'/>
        </form> ";
            break;

        case 'login':
            echo"
            <form method='post' action='/promazavac.php'>
            <p>Uživatelské jméno: <input type='text' name='user' value=''/></p>
            <p>Heslo: <input type='password' name='password' value=''/></p>
            <input type='hidden' name='switch' value='login'/>
            <input type='submit' value='Prihlásit!'/>
            </form>";
            break;

        case 'logout':
            echo"
            <form method='post' action=''/promazavac.php'>
                <input type='hidden' name='switch' value='logout' />
                <input type='submit' value='Odhlásit!'/>
            </form>";
            break;
    }
}

/*
 * Vytiskne obsah stranky
 */
function setContent(){
    if(isset($_SESSION['user'])){
        echo "Přihlášen uživatel " . $_SESSION['user'] . ".";
        addForm("delete");
        addForm("logout");
    } else {
        addForm("login");
    }
}

setHead("Promazávadlo výpisu vyhledávání repozitářů z Git Hubu");
setMenu();
if(isset($_POST['switch'])){
    handlePost();
}
setContent();
setFooter();



