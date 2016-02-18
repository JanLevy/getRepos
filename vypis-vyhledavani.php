<?php
function nactiTridu($trida)
{
    require("tridy/$trida.php");
}
spl_autoload_register("nactiTridu");


Databaze::pripojeni();
$stranka = new Strankovnik('Výpis vyhledávání');
$vyhledavac = new Vyhledavac();

$stranka->printHead();
$stranka->printMenu();
$vyhledavac->getSearches();
$vyhledavac->setPagesLinks();
$stranka->printFooter();


