<?php
function nactiTridu($trida)
{
    require("tridy/$trida.php");
}
spl_autoload_register("nactiTridu");


Databaze::pripojeni();
$stranka = new Strankovnik('Vyhledávání repozitářů');
$gitKonektor = new GitHubConnector();

$stranka->printHead();
$stranka->printMenu();
$gitKonektor->handleGet();
$gitKonektor->setSearchForm();
$stranka->printFooter();

