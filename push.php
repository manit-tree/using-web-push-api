<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define("ROOT", dirname(__FILE__).'/');
require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

function print_object($object) {
    header('content-type: application/json');
    echo json_encode($object);
}

$auth = [
    'VAPID' => [
        'subject' => 'mailto: <8columns@gmail.com>', // can be a mailto: or your website address
        'publicKey' => 'BKFlHXxr1X70rH0Gjn1tkNPwZxg50AVUHgehZmAi36fkXNpe9oJN2ww6s-kcX6ttDI4DtzmeuTL2BkUBIRynX4I',
        'privateKey' => 'RZuAFRQRzTGUWsriBULWNE9qU3X6zZdnqSclZHqM3Q8'
    ]
];

$defaultOptions = [
    'TTL' => 300, // defaults to 4 weeks
    'urgency' => 'normal', // protocol defaults to "normal". (very-low, low, normal, or high)
    'topic' => 'newEvent', // not defined by default. Max. 32 characters from the URL or filename-safe Base64 characters sets
    'batchSize' => 200, // defaults to 1000
];

$webPush = new WebPush($auth, $defaultOptions);
$subscribers = json_decode(file_get_contents(ROOT.'subscribers.json'));

foreach($subscribers as $subscriber) {
    try {
        $subscriber->contentEncoding = 'aesgcm';
        $subscription = Subscription::create((array) $subscriber);
        $payload = '{"message":"Hello World!"}';
        $webPush->queueNotification($subscription, $payload);
    } catch (Exception $e) {
        var_dump($e);
    }
}

foreach ($webPush->flush() as $report) {

}

//$node = json_decode('{"endpoint":"https://fcm.googleapis.com/fcm/send/fVclPKfVw5Q:APA91bHkg43h_SFDLbn6MN5mAI-6E9byQ9626iVGewOPrGGpMwbHDnQKKdl0SU1vCTKi9nWa7AWSR9PJiPMFB5RQNa_QrcIoUvEVmqHR1Chm1clb5wrEwFr07jBsjFIMpadi-Dcde5_Q","expirationTime":null,"keys":{"p256dh":"BCBqVodxNUxEfuiWdIbvFNWZEaHGs0yOUxe8OVttLItyXlwGLi_4DuQqHjZcZ3w7s5hYb4EQoDt7EOdGGQH5BkU","auth":"bZZRu6yA-O8MCtWmvu37JA"}}', true);


// $subscription = Subscription::create($node);


/*
$subscription = Subscription::create([
    "endpoint" => $node['endpoint'],
    "keys" => [
        "auth" => $node['keys']['auth'],
        "p256dh" => $node['keys']['p256dh']
    ]
]);
*/

/*
$subscription = Subscription::create([
    "endpoint" => $node['endpoint'],
    "publicKey" => "BOymTTn5Oh7LQVEXo3-AEtyNiKILjeMk6Le6Hg_msSdFcjPGuLw2xGSOX5yMnPZYpVY2Hcd0qYDf18ID1t0hSMY",
    "authToken" => $node['keys']['auth'],
    "contentEncoding" => "aesgcm"
    ]
);
*/

/*
$subscription = Subscription::create([
    "endpoint" => $node['endpoint'],
    "publicKey" => "BOymTTn5Oh7LQVEXo3-AEtyNiKILjeMk6Le6Hg_msSdFcjPGuLw2xGSOX5yMnPZYpVY2Hcd0qYDf18ID1t0hSMY",
    "contentEncoding" => "aesgcm"
    ]
);
*/

/*
$report = $webPush->sendOneNotification(
    $notifications[0]['subscription'],
    $notifications[0]['payload']
);
*/

// echo json_encode($report);

$res = new stdClass();
$res->status = 200;
$res->description = 'ok';

print_object($res);
?>