<?php
//funkce pro ziskani JSON seznamu repozitaru z pozadovane URL
function get_from_github($url) {
    try {
        $ch = curl_init();
        if(FALSE === $ch){
            throw new Exception('failed to initialize');
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JanLevy');
        $result = curl_exec($ch);
        if (FALSE === $result) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        curl_close($ch);

        return $result;
    }
    catch (Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }
}
//pomocna funkce pro serazeni sestupne podle data vytvoreni repozitare
function cmpcreateddown(array $a, array $b) {
    if ($a['created_at'] > $b['created_at']) {
        return -1;
    } else if ($a['created_at'] < $b['created_at']) {
        return 1;
    } else {
        return 0;
    }
}
?>
<head>
        <meta charset="UTF-8">
        <title>Výpis repozitářů z Git Hubu</title>
    </head>
    <body>
<?php

if(isset($_GET['tofind'])){

    $toFind = htmlspecialchars(trim($_GET['tofind']));
    if (empty($toFind)){
        $error_messages['tofind'] = "Název účtu k vyhledání chybí.";
    }

    if(empty($error_messages)) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $link = mysqli_connect("localhost", "root", "", "getrepos");
        if($link === false){
            die("ERROR: Nepodařilo se připojit. " . mysqli_connect_error());
        }
        $sql = "INSERT INTO vyhledavani VALUES ('', '$ip', '$toFind', '$date')";
        if(!mysqli_query($link, $sql)){
            echo "ERROR: Nepodařilo se provést $sql. " . mysqli_error($link);
        }
        mysqli_close($link);

        $arr = get_from_github('https://api.github.com/users/' . $toFind . '/repos');
        $arr = json_decode($arr, true);
        //prisla-li zpet message, jmeno nema repo
        if(isset($arr['message'])){
            print "Nebyly nalezeny žádné veřejné repozitáře k zadanému uživatelskému jménu.";
        }
        else{
            //var_dump($arr);
            print "<table>
            <th>Repozitář</th>
            <th>Popis</th>
            <th>Vytvořen</th>
            <th>Poslední aktualizace</th>
            <th>Odkaz</th>";
            //vyuziti pomocne funkce pro setrizeni $arr dle data vytvoreni rep
            usort($arr, "cmpcreateddown");
            foreach ($arr as $repo) {
                if($repo['owner']['login']==$toFind) {
                    $testDate = $repo['created_at'];
                    $timestamp = date_create_from_format('Y-m-d\TH:i:s\Z', $testDate);
                    $created = date("Y-m-d H:i:s", date_format($timestamp, 'U'));
                    $testDate = $repo['updated_at'];
                    $timestamp = date_create_from_format('Y-m-d\TH:i:s\Z', $testDate);
                    $updated = date("Y-m-d H:i:s", date_format($timestamp, 'U'));
                    print "<tr>
                        <td>" . $repo['name'] . "</td>
                        <td>" . $repo['description'] . "</td>
                        <td>" . $created . "</td>
                        <td>" . $updated . "</td>
                        <td>" . $repo['html_url'] . "</td>
                       </tr>
                ";
                }
            }
            print "</table>";
        }
    }
} ?>
<div>
<form method="get" action="index.php">
    <input type="text" name="tofind" value="" id="get" />
    <?php
    if(isset($error_messages['tofind'])){
        echo $error_messages['tofind'];
    } else{
        echo "<label for=\"get\">Zadejte název účtu k vyhledání</label>";
    }
    ?>
    <input type="submit" value="Vyhledat!"/>
    <p>Výpis vyhledávání <a href = '/vypis-vyhledavani.php/'>zde.</a></p>
</form>
    </div>
    </body>

