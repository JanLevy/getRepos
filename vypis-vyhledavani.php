<?php
function right_int($arg) {
    $arg = (int) $arg;

    if (is_numeric($arg)) {
        return $arg;
    }
    // v případě, že $arg neprojde kontrolou, bude naše $page 1 => začátek
    return 1;
}
define ("ON_PAGE", 10);
?>

<head>
        <meta charset="UTF-8">
        <title>Výpis vyhledávání repozitářů z Git Hubu</title>
    </head>
    <body>
<?php

$link = mysqli_connect("localhost", "root", "", "getrepos");
if($link === false){
    die("ERROR: Nepodařilo se připojit. " . mysqli_connect_error());
}


$count = "SELECT COUNT(*) FROM vyhledavani";
$num = mysqli_query($link, $count);
$max = mysqli_fetch_row($num)[0];
mysqli_free_result($num);
if(!isset($_GET["page"])){
    $page = 1;
}
else{
    $page = right_int($_GET["page"]);
}
$by = (ON_PAGE * ($page - 1));

$sql = "SELECT * FROM vyhledavani ORDER BY Datum desc LIMIT ".ON_PAGE." OFFSET " . $by ;
$res = mysqli_query($link, $sql);
if(!$res){
    echo "ERROR: Nepodařilo se provést $sql. " . mysqli_error($link);
} else {

    if (mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)) {
            echo "Uživatel " . $row["IP"]. " položil dotaz na \"" . $row["Dotaz"]. "\" dne " . $row["Datum"]. ".<br>";
        }
    } else {
        echo "Nepodařilo se nalézt žádné vyhledávání.";
    }
}
if(ON_PAGE < $max) {
    if ($page > 1) {
        echo "<a href='/vypis-vyhledavani.php/?page=1'>&lt;&lt;</a>";
        echo "<a href='/vypis-vyhledavani.php/?page=" . ($page - 1) . "'> &lt;</a>";

        for ($i = 4; $i > 0; $i--) {
            if (($page - $i) >= 1) {
                echo "<a href='/vypis-vyhledavani.php/?page=" . ($page - $i) . "'> " . ($page - $i) . "</a>";
            }
        }
    }
    echo " " . $page;
}


if($page < ($max / ON_PAGE)) {
    for($i = 1; $i < 4; $i++) {
        if(($page + $i) <= ceil($max / ON_PAGE)) {
            echo "<a href='/vypis-vyhledavani.php/?page=".($page+$i)."'> ".($page+$i)."</a>";
        }
    }
    echo "<a href='/vypis-vyhledavani.php/?page=".ceil($max / ON_PAGE)."' >&gt;&gt;</a>";
}

mysqli_close($link);
echo "<p>Vyhledávání <a href='/index.php'>zde</a>.</p>";
?>
    </body>
