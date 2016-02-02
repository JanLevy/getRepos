<?php
session_start();
function auth ($user, $password){
    $link = mysqli_connect("localhost", "root", "", "getrepos");
    if($link === false){
        die("ERROR: Nepodařilo se připojit. " . mysqli_connect_error());
    }
    $sql = "SELECT password FROM pass WHERE user=" . $user;
    $res = mysqli_query($link, $sql);
    if(!mysqli_query($link, $sql)){
        mysqli_close($link);
        return false;
    }
    if (mysqli_num_rows($res) === 1) {
        $row = mysqli_fetch_assoc($res);
        $hash = $row["password"];
        mysqli_close($link);
        return password_verify($password, $hash);
        }
    else {
        return false;
    }
}

if(isset($_POST['login'])){
    $user = htmlspecialchars(trim($_POST['user']));
    $password = htmlspecialchars(trim($_POST['password']));

    if(auth($user, $password)){
        $_SESSION['user']=$user;
    }
    header( "Location: promazavac.php");
}
if(isset($_POST['logout'])){
    unset($_SESSION['user']);
    session_destroy();
    header("Location: promazavac.php");
}
if(isset($_POST['delete'])){
    $link = mysqli_connect("localhost", "root", "", "getrepos");
    if($link === false){
        die("ERROR: Nepodařilo se připojit. " . mysqli_connect_error());
    }
    $insec=$_POST['todelete']*3600;
    $deletefrom = date('Y-m-d H:i:s', time()-$insec);
    $sql = "DELETE FROM vyhledavani WHERE (Datum <'" . $deletefrom . "')";
    $res = mysqli_query($link, $sql);
    if(!mysqli_query($link, $sql)){
        echo "ERROR: Nepodařilo se provést $sql. " . mysqli_error($link);
        mysqli_close($link);
    } else {
        echo "Vyhledávání dřívější než " . $deletefrom . " byla promazána.";
    }

}

?>
    <head>
        <meta charset="UTF-8">
        <title>Promazávadlo výpisu vyhledávání repozitářů z Git Hubu</title>
    </head>
    <body>

<?php
if(isset($_SESSION['user'])){
    echo "Přihlášen uživatel " . $_SESSION['user'] . ".";
    ?>
    <form method="post" action="/promazavac.php">
        <p>Chci smazat vyhledávání starší <input type="number" name="todelete" min="0"> hodin.</p>
        <input type="hidden" name="delete" />
        <input type="submit" value="Smazat!"/>
    </form>
    <?php
    ?>
    <form method="post" action="/promazavac.php">
        <input type="hidden" name="logout" />
        <input type="submit" value="Odhlásit!"/>
    </form>
    <?php
} else {
    ?>
    <form method="post" action="/promazavac.php">
        <p>Uživatelské jméno: <input type="text" name="user" value="" /></p>
        <p>Heslo: <input type="password" name="password" value="" /></p>
        <input type="hidden" name="login" />
        <input type="submit" value="Prihlásit!"/>
    </form>
    <?php
}



