<?php
session_start();
if (isset($_SESSION['user']) && $_SESSION['timeout'] + 600 < time()) {
    unset($_SESSION['user']);
    session_destroy();
} else {
    $_SESSION['timeout'] = time();
}
function nactiTridu($trida)
{
    require("tridy/$trida.php");
}
spl_autoload_register("nactiTridu");


Databaze::pripojeni();
$stranka = new Strankovnik('Promazávání výpisu');
$promazavac = new Promazavac();

$stranka->printHead();
$stranka->printMenu();
if(isset($_POST['switch'])){
    $promazavac->handlePost();
}
$promazavac->setContent();
$stranka->printFooter();



