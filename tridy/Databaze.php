<?php

class Databaze {

    private static $spojeni;

    /*
     * Nastaveni pripojeni
     */
    private static $nastaveni = Array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES => false
    );

    /*
     * Pripojeni k db, vraci instanci pripojeni
     */
    public static function pripojeni() {
        if (!isset(self::$spojeni)) {
            $config = parse_ini_file('/../config.ini');
            $server = $config['server'];
            $user = $config['user'];
            $password = $config['password'];
            $db = $config['db'];
            self::$spojeni = @new PDO(
                "mysql:host=$server;dbname=$db",
                $user,
                $password,
                self::$nastaveni
            );
        }
        return self::$spojeni;
    }

    /*
     * Funkce pro dotazovani na db, vraci odpoved na dotaz
     */
    public static function dotaz($sql, $parametry = array()) {
        $dotaz = self::$spojeni->prepare($sql);
        $dotaz->execute($parametry);
        return $dotaz;
    }
}
