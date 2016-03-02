<?php

class GitHubConnector
{
    private $error_messages;

    public function __construct(){
    }

    /*
     * Zpracuje vyhledavani, ktere prislo v GET
     * Zapise vyhledavane heslo do DB
     * Dotaze se na API GitHubu
     * Vytiskne vysledek
     */
    public function handleGet(){

        if(isset($_GET['tofind'])){
            $toFind = htmlspecialchars(trim($_GET['tofind']));

            if (empty($toFind)){
                $this->error_messages = "Název účtu k vyhledání chybí. Zadejte jej prosím.";
            }

            if(empty($this->error_messages)) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

                Databaze::dotaz('INSERT INTO vyhledavani VALUES (?, ?, ?, ?)', array('', $ip, $toFind, $date));
                $repos = $this->getFromGithub($toFind);
                $repos = json_decode($repos, true);

                //prisla-li zpet message, jmeno nema repo
                if(isset($arr['message'])){
                    print "Nebyly nalezeny žádné veřejné repozitáře k zadanému uživatelskému jménu.";
                }

                else{
                    $this->printRepos($repos, $toFind);
                }
            }
        }
    }

    /*
        * Vrati JSON seznam repozitaru z pozadovane URL
        */
    private function getFromGithub($toFind) {
        $url = 'https://api.github.com/users/' . $toFind . '/repos';
        try {
            $ch = curl_init();

            if(FALSE === $ch){
                throw new Exception('failed to initialize');
            }

            $config = parse_ini_file('/../config.ini');
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
     * Vytiskne seznam repozitaru
     */
    private function printRepos($repos, $toFind){
        print "<div class='content'><table>
        <th>Repozitář</th>
        <th>Popis</th>
        <th>Vytvořen</th>
        <th>Poslední aktualizace</th>";
        usort($repos, array($this, "orderDesc"));

        foreach ($repos as $repo) {
            if($repo['owner']['login']==$toFind) {
                $helperCreated = $repo['created_at'];
                $timestampCreated = date_create_from_format('Y-m-d\TH:i:s\Z', $helperCreated);
                $created = date("Y-m-d H:i:s", date_format($timestampCreated, 'U'));
                $helperUpdated = $repo['updated_at'];
                $timestampUpdated = date_create_from_format('Y-m-d\TH:i:s\Z', $helperUpdated);
                $updated = date("Y-m-d H:i:s", date_format($timestampUpdated, 'U'));
                $url = $repo['html_url'];
                print "<tr>
                <td><a href='" . $url . "'>" . $repo['name'] . "</a></td>
                <td>" . $repo['description'] . "</td>
                <td>" . $created . "</td>
                <td>" . $updated . "</td>
                </tr>";
            }
        }
        print "</table></div>";
    }

    /*
     * Pomocna funkce pro serazeni repozitaru sestupne podle data vytvoreni repozitare
     */
    private function orderDesc(array $a, array $b) {
        if ($a['created_at'] > $b['created_at']) {
            return -1;
        } else if ($a['created_at'] < $b['created_at']) {
            return 1;
        } else {
            return 0;
        }
    }


    /*
     * Vypise vyhledavaci formular
     */
    function setSearchForm(){
        echo "
        <div id='search' class='content'>
        <form method='get' action='/index.php'>";

        if(isset($this->error_messages)){
            echo "<label for='tofind'> " . $this->error_messages . "</label>";
            unset($this->error_messages);
        } else{
            echo "<label for='tofind'> Zadejte název účtu k vyhledání:</label>";
        }

        echo "
        <input type='text' name='tofind' value='' id='tofind' />
        <input type='submit' value='Vyhledat!'/>
        </form>
        </div>";
    }
}