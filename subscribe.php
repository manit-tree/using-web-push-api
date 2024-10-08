<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define("ROOT", dirname(__FILE__).'/');

function print_object($object) {
    header('content-type: application/json');
    echo json_encode($object);
}

try {
    $node = json_decode(file_get_contents('php://input'));
    $res = new stdClass();

    if ($node) {
        $subscribers = (array) json_decode(file_get_contents(ROOT.'subscribers.json'));
        $hash = md5(json_encode($node));

    
        if (!array_key_exists($hash, $subscribers)) {
            $subscribers[$hash] = $node;
            file_put_contents(ROOT.'subscribers.json', json_encode($subscribers));
        }


        $res->status = 200;
        $res->description = 'ok';
    } else {
        $res->status = 501;
        $res->description = 'invalide json format';
    }

    print_object($res);
} catch (Exception $e) {
    var_dump($e);
}
?>