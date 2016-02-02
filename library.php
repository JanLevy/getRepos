<?php

function setHead($title){
    echo "
        <head>
            <meta charset=\"UTF-8\">
            <title>" . $title . "</title>
        </head>
        <body>";
}


function setMenu(){
    echo "
    <ul>
        <li><a href = '/index.php/'>Vyhledávání repozitářů</a></li>
        <li><a href = '/vypis-vyhledavani.php/'>Výpis vyhledávání</a></li>
        <li><a href = '/promazavac.php/'>Promazávání výpisu</a></li>
    </ul>
    ";
}
function setFooter(){
    echo "
        <div>
            '&copy;' GetRepos 2016
        </div>
    </body>
    ";
}

function dbQuery($sql){
    $config = parse_ini_file('/config.ini');
    $server = $config['server'];
    $user = $config['user'];
    $password = $config['password'];
    $db = $config['db'];
    $link = mysqli_connect($server, $user, $password, $db);
    if($link === false){
        die("ERROR: Nepodařilo se připojit. " . mysqli_connect_error());
    }
    $query = mysqli_query($link, $sql);
    $toReturn = array('query'=>$query, 'link'=>$link);
    return $toReturn;
}
