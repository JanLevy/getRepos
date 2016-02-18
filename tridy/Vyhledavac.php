<?php


class Vyhledavac
{
    const onPage = 10;

    public function __construct(){

    }

    /*
     * Ziska zaznamy pro aktualni stranky a vytiskne je
     */
    public function getSearches(){
        $page = $this->getPage();
        $by = (self::onPage * ($page - 1));
        $query = Databaze::dotaz('SELECT * FROM vyhledavani ORDER BY Datum desc LIMIT ? OFFSET ?', array(self::onPage, $by));
        if(!$query){
            echo "ERROR: Nepodařilo se provést vyhledávání.";
        } else {
            $this->printSearches($query);
        }
    }

    /*
     * Vytiskne odkazy na ostatni stranky strankovaneho seznamu
     */
    public function setPagesLinks(){
        $max = $this->getMax();
        $page = $this->getPage();

        echo "<div class='content'>";
        if(self::onPage < $max) {
            if ($page > 1) {
                $this->printLinksDown($page);
            }
            echo " " . $page;
        }

        if($page < ($max / self::onPage)) {
            $this->printLinksUp($page, $max);
        }
        echo "</div>";
    }

    /*
     * Priradi stranku ve strankovanem seznamu
     */
    private function getPage(){
        if(!isset($_GET["page"])){
            $page = 1;
        }
        else{
            $page = $this->checkInt($_GET["page"]);
        }
        return $page;
    }

    /*
     * Pro aktualni stranku ze seznamu vytiskne zaznamy v DB o vyhledavani
     */
    private function printSearches($query){
        $counter = 0;
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $counter++;
            if($counter === 1){
                echo "<div class='content'>
            <table>
            <th>Uživatel</th>
            <th>Dotaz</th>
            <th>Datum a čas</th>";
            }
            echo "<tr><td>" . $row["IP"]. "</td>
                <td>\"" . $row["Dotaz"]. "\"</td>
                <td>" . $row["Datum"]. "</td></tr>";
        }
        if ($counter > 0){
            echo "</table></div>";
        } else {
            echo "Nepodařilo se nalézt žádné vyhledávání.";
        }
    }


    /*
     * Vrati pocet zaznamu v DB o vyhledavani
     */
    private function getMax(){
        $query = Databaze::dotaz('SELECT COUNT(*) FROM vyhledavani');
        $res = $query->fetch(PDO::FETCH_NUM);
        if($res !== false){
            $max = $res[0];
        } else{
            $max = 0;
        }
        return $max;
    }

    /*
     * Vytiskne odkazy na stranky s nizsim cislem, nez je to aktualni
     */
    private function printLinksDown($page){
        echo "<a href='/vypis-vyhledavani.php/?page=1'>&lt;&lt;</a>";
        echo "<a href='/vypis-vyhledavani.php/?page=" . ($page - 1) . "'> &lt;</a>";

        for ($i = 4; $i > 0; $i--) {
            if (($page - $i) >= 1) {
                echo "<a href='/vypis-vyhledavani.php/?page=" . ($page - $i) . "'> " . ($page - $i) . "</a>";
            }
        }
    }

    /*
     * Vytiskne odkazy na stranky s vyzsim cislem, nez je to aktualni
     */
    private function printLinksUp($page, $max){
        for($i = 1; $i < 4; $i++) {
            if(($page + $i) <= ceil($max / self::onPage)) {
                echo "<a href='/vypis-vyhledavani.php/?page=".($page+$i)."'> ".($page+$i)."</a>";
            }
        }
        echo "<a href='/vypis-vyhledavani.php/?page=" . ($page + 1) . "'>&gt; </a>";
        echo "<a href='/vypis-vyhledavani.php/?page=".ceil($max / self::onPage)."' >&gt;&gt;</a>";
    }


    /*
     * Overi, ze zadany parametr je cislo, pripadne uzivatele vrati na 1. stranu seznamu
     */
    private function checkInt($arg) {
        $arg = (int) $arg;

        if (is_numeric($arg)) {
            return $arg;
        }
        // v případě, že $arg neprojde kontrolou, bude naše $page 1 => začátek
        return 1;
    }

}