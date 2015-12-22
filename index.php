<?php
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

$arr = get_from_github('https://api.github.com/users/jan-burianek/repos');
$arr = json_decode($arr, true);


foreach ($arr as $repo){
    print $repo['name'] . "</br>";
}
