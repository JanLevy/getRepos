<?php
include 'library.php';
$error_messages = null;

/*
 * Vrati JSON seznam repozitaru z pozadovane URL
 */
function getFromGithub($url) {
    try {
        $ch = curl_init();

        if(FALSE === $ch){
            throw new Exception('failed to initialize');
        }

        $config = parse_ini_file('/config.ini');
        $account = $config['account'];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $account);
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

/*
 * Pomocna funkce pro serazeni repozitaru sestupne podle data vytvoreni repozitare
 */
function orderDesc(array $a, array $b) {
    if ($a['created_at'] > $b['created_at']) {
        return -1;
    } else if ($a['created_at'] < $b['created_at']) {
        return 1;
    } else {
        return 0;
    }
}

/*
 * Zpracuje vyhledavani
 */
function handleGet(){
    global $error_messages;

    if(isset($_GET['tofind'])){
        $toFind = htmlspecialchars(trim($_GET['tofind']));

        if (empty($toFind)){
            $error_messages = "Název účtu k vyhledání chybí. Zadejte jej prosím.";
        }

        if(empty($error_messages)) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

            $sql = "INSERT INTO vyhledavani VALUES ('', '$ip', '$toFind', '$date')";
            $query = dbQuery($sql);

            if(!$query['query']){
                echo "ERROR: Nepodařilo se provést $sql. " . mysqli_error($query['link']);
            }
            mysqli_close($query['link']);

            $arr = getFromGithub('https://api.github.com/users/' . $toFind . '/repos');
            $arr = json_decode($arr, true);
            //prisla-li zpet message, jmeno nema repo

            if(isset($arr['message'])){
                print "Nebyly nalezeny žádné veřejné repozitáře k zadanému uživatelskému jménu.";
            }

            else{
                printRepos($arr, $toFind);
            }
        }
    }
}

/*
 * Vytiskne seznam repozitaru
 */
function printRepos($arr, $toFind){
    print "<table>
        <th>Repozitář</th>
        <th>Popis</th>
        <th>Vytvořen</th>
        <th>Poslední aktualizace</th>";
    usort($arr, "orderDesc");

    foreach ($arr as $repo) {
        if($repo['owner']['login']==$toFind) {
            $testDate = $repo['created_at'];
            $timestamp = date_create_from_format('Y-m-d\TH:i:s\Z', $testDate);
            $created = date("Y-m-d H:i:s", date_format($timestamp, 'U'));
            $testDate = $repo['updated_at'];
            $timestamp = date_create_from_format('Y-m-d\TH:i:s\Z', $testDate);
            $updated = date("Y-m-d H:i:s", date_format($timestamp, 'U'));
            $url = $repo['html_url'];
            print "<tr>
                <td><a href='\" . $url . \"'>" . $repo['name'] . "</a></td>
                <td>" . $repo['description'] . "</td>
                <td>" . $created . "</td>
                <td>" . $updated . "</td>
                </tr>";
        }
    }
    print "</table>";
}

/*
 * Vypise vyhledavaci formular
 */
function setSearchForm(){
    global $error_messages;
    echo "
        <div>
        <form method='get' action='/index.php'>
        <input type='text' name='tofind' value='' id='tofind' />";

    if(isset($error_messages)){
        echo "<label for='tofind'>" . $error_messages . "</label>";
        unset($GLOBALS['error_messages']);
    } else{
        echo "<label for='tofind'>Zadejte název účtu k vyhledání.</label>";
    }

    echo "
        <input type='submit' value='Vyhledat!'/>
        </form>
        </div>";
}

setHead("Výpis repozitářů z GitHubu");
setMenu();
handleGet();
setSearchForm();
setFooter();

