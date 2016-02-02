<?php
include 'library.php';
define("ON_PAGE", 3);

function checkInt($arg) {
    $arg = (int) $arg;
    if (is_numeric($arg)) {
        return $arg;
    }
    // v případě, že $arg neprojde kontrolou, bude naše $page 1 => začátek
    return 1;
}

function getMax(){
    $sql = "SELECT COUNT(*) FROM vyhledavani";
    $query = dbQuery($sql);
    $num = $query['query'];
    if(!$num){
        $max = 0;
    } else{
        $max = mysqli_fetch_row($num)[0];
    }
    mysqli_close($query['link']);
    return $max;
}

function getPage(){
    if(!isset($_GET["page"])){
        $page = 1;
    }
    else{
        $page = checkInt($_GET["page"]);
    }
    return $page;
}

function vypisOdkazyNaNizsi($page){
    echo "<a href='/vypis-vyhledavani.php/?page=1'>&lt;&lt;</a>";
    echo "<a href='/vypis-vyhledavani.php/?page=" . ($page - 1) . "'> &lt;</a>";

    for ($i = 4; $i > 0; $i--) {
        if (($page - $i) >= 1) {
            echo "<a href='/vypis-vyhledavani.php/?page=" . ($page - $i) . "'> " . ($page - $i) . "</a>";
        }
    }
}

function vypisOdkazyNaVyssi($page, $max){
    for($i = 1; $i < 4; $i++) {
        if(($page + $i) <= ceil($max / ON_PAGE)) {
            echo "<a href='/vypis-vyhledavani.php/?page=".($page+$i)."'> ".($page+$i)."</a>";
        }
    }
    echo "<a href='/vypis-vyhledavani.php/?page=" . ($page + 1) . "'>&gt; </a>";
    echo "<a href='/vypis-vyhledavani.php/?page=".ceil($max / ON_PAGE)."' >&gt;&gt;</a>";
}

function setOdkazyNaStranky(){
    $max = getMax();
    $page = getPage();
    if(ON_PAGE < $max) {
        if ($page > 1) {
            vypisOdkazyNaNizsi($page);
        }
        echo " " . $page;
    }
    if($page < ($max / ON_PAGE)) {
        vypisOdkazyNaVyssi($page, $max);
    }
}

function vypisVyhledavani(){
    $page = getPage();
    $by = (ON_PAGE * ($page - 1));
    $sql = "SELECT * FROM vyhledavani ORDER BY Datum desc LIMIT ".ON_PAGE." OFFSET " . $by ;
    $query = dbQuery($sql);
    $res=$query['query'];

    if(!$res){
        echo "ERROR: Nepodařilo se provést $sql. " . mysqli_error($query['link']);
    } else {
        if (mysqli_num_rows($res) > 0) {
            while($row = mysqli_fetch_assoc($res)) {
                echo "Uživatel " . $row["IP"]. " položil dotaz na \"" . $row["Dotaz"]. "\" dne " . $row["Datum"]. ".<br>";
            }
        } else {
            echo "Nepodařilo se nalézt žádné vyhledávání.";
        }
    }
    mysqli_close($query['link']);
}

setHead("Výpis vyhledávání repozitářů z Git Hubu");
setMenu();
vypisVyhledavani();
setOdkazyNaStranky();
setFooter();


