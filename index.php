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
        $arr = get_from_github('https://api.github.com/users/' . $toFind . '/repos');
        $arr = json_decode($arr, true);
        //prisla-li zpet message, jmeno nema repo
        if(isset($arr['message'])){
            print "Nebyly nalezeny žádné veřejné repozitáře k zadanému uživatelskému jménu.";
        }
        else{
            //vyuziti pomocne funkce pro setrizeni $arr dle data vytvoreni rep
            usort($arr, "cmpcreateddown");
            foreach ($arr as $repo) {
                print $repo['name'] . " " . $repo['created_at'] . "</br>";
            }
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
</form>
    </div>
    </body>

