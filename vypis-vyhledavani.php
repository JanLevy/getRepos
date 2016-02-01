<?php ?>
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
$sql = "SELECT * FROM vyhledavani ORDER BY Datum desc";
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
mysqli_close($link);
?>
    </body>
