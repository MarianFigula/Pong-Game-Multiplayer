<?php

use Workerman\Worker;
use Workerman\Lib\Timer;

require_once __DIR__ . '/vendor/autoload.php';

$clients = array();
$clientOrders = array();

$app_width = 810;
$app_height = 600;

$ball_x = $app_width / 2;
$ball_y = $app_height / 2;
$ballSize = 15;
$ballSpeedX = 2.5;
$ballSpeedY = 2.5;
$hitCounter = 0;

$paddleSpeed = 10;
$num_players = 0;
$borderRectWidth = 20;
$paddleWidth = 20;
$paddleHeight = 100;
$paddleSpacing = 65;
$button_start = false;

$ball = new stdClass();
$ball->x = $ball_x;
$ball->y = $ball_y;
$ball->width = $ballSize;
$ball->height = $ballSize;
$ball->ball_size = $ballSize;
$ball->ball_speedX = $ballSpeedX;
$ball->ball_speedY = $ballSpeedY;

$paddleL = new stdClass();
$paddleL->speed = $paddleSpeed;
$paddleL->x = $paddleSpacing;
$paddleL->y = $app_height / 2 - $paddleHeight / 2;
$paddleL->width = $paddleWidth;
$paddleL->height = $paddleHeight;
$paddleL->lives = 3;
$paddleL->player_name = null;
$paddleL->id = null;


$paddleR = new stdClass();
$paddleR->speed = $paddleSpeed;
$paddleR->x = $app_width - $paddleWidth -  $paddleSpacing;
$paddleR->y = $app_height / 2 - $paddleHeight / 2;
$paddleR->width = $paddleWidth;
$paddleR->height = $paddleHeight;
$paddleR->lives = 3;
$paddleR->player_name = null;
$paddleR->id = null;

$paddleT = new stdClass();
$paddleT->speed = $paddleSpeed;
$paddleT->x = $app_width / 2 - $paddleHeight / 2;
$paddleT->y = $paddleSpacing;
$paddleT->width = $paddleHeight + 10;
$paddleT->height = $paddleWidth;
$paddleT->lives = 3;
$paddleT->player_name = null;
$paddleT->id = null;

$paddleB = new stdClass();
$paddleB->speed = $paddleSpeed;
$paddleB->x = $app_width / 2 - $paddleHeight / 2;
$paddleB->y = $app_height - $paddleWidth - $paddleSpacing;
$paddleB->width = $paddleHeight + 10;
$paddleB->height = $paddleWidth;
$paddleB->lives = 3;
$paddleB->player_name = null;
$paddleB->id = null;

$game_data = new stdClass();
$game_data->ball = $ball;
$game_data->paddleL = $paddleL;
$game_data->paddleR = $paddleR;
$game_data->paddleT = $paddleT;
$game_data->paddleB = $paddleB;
$game_data->num_players = $num_players;
$game_data->game_started = false;
$game_data->hit_counter = 0;

$net_left = new stdClass();
$net_left->x = 0;
$net_left->y = 100;
$net_left->width = 36;
$net_left->height = 397;

$net_right = new stdClass();
$net_right->x = 775;
$net_right->y = 100;
$net_right->width = 36;
$net_right->height = 397;

$net_top = new stdClass();
$net_top->x = 160;
$net_top->y = 0;
$net_top->width = 490;
$net_top->height = 38;

$net_bottom = new stdClass();
$net_bottom->x = 163;
$net_bottom->y = 573;
$net_bottom->width = 486;
$net_bottom->height = 38;

$allCons = array();
$start_who = 1;


function testForAABB($object1, $object2){
    return $object1->x < $object2->x + $object2->width
        && $object1->x + $object1->width > $object2->x
        && $object1->y < $object2->y + $object2->height
        && $object1->y + $object1->height > $object2->y;
}

function gameInit(){
    global $game_data,$ball, $paddleL, $paddleR, $paddleT, $paddleB, $ball_x, $ball_y, $ballSpeedX, $ballSpeedY,
           $ballSize, $paddleSpacing, $paddleSpeed, $app_width, $app_height, $paddleWidth, $paddleHeight;

    $ballSpeedX = 2.5;
    $ballSpeedY = 2.5;

    $ball = new stdClass();
    $ball->x = $ball_x;
    $ball->y = $ball_y;
    $ball->width = $ballSize;
    $ball->height = $ballSize;
    $ball->ball_size = $ballSize;
    $ball->ball_speedX = $ballSpeedX;
    $ball->ball_speedY = $ballSpeedY;

    $paddleL = new stdClass();
    $paddleL->speed = $paddleSpeed;
    $paddleL->x = $paddleSpacing;
    $paddleL->y = $app_height / 2 - $paddleHeight / 2;
    $paddleL->width = $paddleWidth;
    $paddleL->height = $paddleHeight;
    $paddleL->lives = $game_data->paddleL->lives;
    $paddleL->player_name = $game_data->paddleL->player_name;
    //$paddleL->id = $game_data->paddleL->id;

    $paddleR = new stdClass();
    $paddleR->speed = $paddleSpeed;
    $paddleR->x = $app_width - $paddleWidth -  $paddleSpacing;
    $paddleR->y = $app_height / 2 - $paddleHeight / 2;
    $paddleR->width = $paddleWidth;
    $paddleR->height = $paddleHeight;
    $paddleR->lives = $game_data->paddleR->lives;
    $paddleR->player_name = $game_data->paddleR->player_name;
    //$paddleR->id = $game_data->paddleR->id;;

    $paddleT = new stdClass();
    $paddleT->speed = 5;
    $paddleT->x = $app_width / 2 - $paddleHeight / 2;
    $paddleT->y = $paddleSpacing;
    $paddleT->width = $paddleHeight + 10;
    $paddleT->height = $paddleWidth;
    $paddleT->lives = $game_data->paddleT->lives;
    $paddleT->player_name = $game_data->paddleT->player_name;
    //$paddleT->id = $game_data->paddleT->id;;

    $paddleB = new stdClass();
    $paddleB->speed = $paddleSpeed;
    $paddleB->x = $app_width / 2 - $paddleHeight / 2;
    $paddleB->y = $app_height - $paddleWidth - $paddleSpacing;
    $paddleB->width = $paddleHeight + 10;
    $paddleB->height = $paddleWidth;
    $paddleB->lives = $game_data->paddleB->lives;
    $paddleB->player_name = $game_data->paddleB->player_name;
    //$paddleB->id = $game_data->paddleB->id;;

    $game_data->ball = $ball;
    $game_data->paddleL = $paddleL;
    $game_data->paddleR = $paddleR;
    $game_data->paddleT = $paddleT;
    $game_data->paddleB = $paddleB;


}

function netIsHit(){
    global $game_data, $net_left, $net_right, $net_top, $net_bottom;

    if ($game_data->paddleL->player_name != null){
        if (testForAABB($net_left, $game_data->ball)){
            //echo "lavy minus zivot";
            $game_data->paddleL->lives--;
            if($game_data->paddleL->lives < 0){
                $game_data->paddleL->lives = 0;
            }
            if($game_data->paddleL->lives == 0){
                $game_data->paddleL->player_name = null;
                $game_data->num_players--;
            }
            return true;
        }
    }
    if ($game_data->paddleR->player_name != null){
        if (testForAABB($net_right, $game_data->ball)){
            //echo "test R";
            //echo "pravy minus zivot";
            $game_data->paddleR->lives--;
            if($game_data->paddleR->lives < 0){
                $game_data->paddleR->lives = 0;
            }
            if($game_data->paddleR->lives == 0){
                $game_data->paddleR->player_name = null;
                $game_data->num_players--;
            }
            return true;
        }
    }
    if ($game_data->paddleT->player_name != null){
        if (testForAABB($net_top, $game_data->ball)){
            $game_data->paddleT->lives--;
            if($game_data->paddleT->lives < 0){
                $game_data->paddleT->lives = 0;
            }
            if($game_data->paddleT->lives == 0){
                $game_data->paddleT->player_name = null;
                $game_data->num_players--;
            }
            return true;
        }
    }
    if ($game_data->paddleB->player_name != null){
        if (testForAABB($net_bottom, $game_data->ball)){
            $game_data->paddleB->lives--;
            if($game_data->paddleB->lives < 0){
                $game_data->paddleB->lives = 0;
            }
            if($game_data->paddleB->lives == 0){
                $game_data->paddleB->player_name = null;
                $game_data->num_players--;
            }
            return true;
        }
    }
    return false;
}

$ballSpeed = 1.01;

function gameLoop() {
    //echo "DSA";
    global $game_data, $ballSpeed, $paddleL, $paddleB, $paddleT, $paddleR, $ballSpeedX,$ballSpeedY, $borderRectWidth, $app_height, $app_width, $ballSize;

    if ($game_data->game_started == false){
        $paddleL->lives = 3;
        $paddleR->lives = 3;
        $paddleT->lives = 3;
        $paddleB->lives = 3;
        $paddleL->player_name = null;
        $paddleR->player_name = null;
        $paddleT->player_name = null;
        $paddleB->player_name = null;
        gameInit();
        return;
    }
    $game_data->ball->x += $ballSpeedX;
    $game_data->ball->y += $ballSpeedY;

    if (netIsHit()){
        gameInit();
        return;
    }

    if ($game_data->ball->y - $ballSize < $borderRectWidth || $game_data->ball->y + $ballSize > $app_height - $borderRectWidth){
        $ballSpeedY = -($ballSpeedY * $ballSpeed);
        $game_data->hit_counter++;
    }
    if ($game_data->ball->x - $ballSize < $borderRectWidth || $game_data->ball->x + $ballSize > $app_width - $borderRectWidth){
        $ballSpeedX = -($ballSpeedX * $ballSpeed);
        $game_data->hit_counter++;

    }
    if ($game_data->paddleL->player_name != null){
        if ($game_data->ball->x < $game_data->paddleL->x + $game_data->paddleL->width && $game_data->ball->y > $game_data->paddleL->y && $game_data->ball->y < $game_data->paddleL->y + $game_data->paddleL->height) {
            $ballSpeedX = -($ballSpeedX + $ballSpeed);
            $game_data->hit_counter++;

        }
    }
    if ($game_data->paddleR->player_name != null) {
        if ($game_data->ball->x > $game_data->paddleR->x && $game_data->ball->y > $game_data->paddleR->y && $game_data->ball->y < $game_data->paddleR->y + $game_data->paddleR->height) {
            $ballSpeedX = -($ballSpeedX + $ballSpeed);
            $game_data->hit_counter++;

        }
    }
    if ($game_data->paddleT->player_name != null) {
        if ($game_data->ball->y < $game_data->paddleT->y + $game_data->paddleT->height &&
            $game_data->ball->x > $game_data->paddleT->x && $game_data->ball->x < $game_data->paddleT->x + $game_data->paddleT->width) {
            $ballSpeedY = -($ballSpeedY + $ballSpeed);
            $game_data->hit_counter++;

        }
    }
    if ($game_data->paddleB->player_name != null) {
        if ($game_data->ball->y > $game_data->paddleB->y && $game_data->ball->x > $game_data->paddleB->x &&
            $game_data->ball->x < $game_data->paddleB->x + $game_data->paddleB->width) {
            $ballSpeedY = -($ballSpeedY + $ballSpeed);
            $game_data->hit_counter++;

        }
    }
}

function sendGameData()
{
    global $game_data;

    $game_data = $GLOBALS["game_data"];

    return json_encode($game_data);
}

// SSL context.
$context = [
    'ssl' => [
        'local_cert' => '/home/xfigula/webte_fei_stuba_sk.pem',
        'local_pk' => '/home/xfigula/webte.fei.stuba.sk.key',
        'verify_peer' => false,
    ]
];

// Create A Worker and Listens 9000 port, use Websocket protocol
$ws_worker = new Worker("websocket://0.0.0.0:9000", $context);

// Enable SSL. WebSocket+SSL means that Secure WebSocket (wss://).
// The similar approaches for Https etc.
$ws_worker->transport = 'ssl';

// 1 process
$ws_worker->count = 1;

$ws_worker->onWorkerStart = function($ws_worker) {
    Timer::add(0.02, function () use ($ws_worker) {
        global $game_data;

        if (gettype($game_data) == "string"){
            $game_data = json_decode($game_data);
        }
        if ($game_data->game_started == true){
            gameLoop();

        }
        foreach ($ws_worker->connections as $connection) {
            $connection->send(sendGameData());
        }
    });

// Emitted when new connection come
    $ws_worker->onConnect = function ($connection) use (&$clients, &$clientOrders,$ws_worker) {
        // Emitted when websocket handshake done
        global $allCons, $game_data;

        echo $connection->id;

        $connection->onWebSocketConnect = function ($connection) {
            $connection->send(sendGameData());
        };
    };

    $ws_worker->onMessage = function ($connection, $data) {
        global $game_data, $allCons;

        if (property_exists(json_decode($data), "type") && json_decode($data)->type == "buttonJoin"){
            $allCons[] = $connection;


            $game_data->num_players = json_decode($data)->data;
            $tmp = 0;
            switch ($game_data->num_players){ // $connection->id % 4
                case 1:$game_data->paddleL->id = $connection->id; $tmp = $connection->id; break;
                case 2:$game_data->paddleR->id = $connection->id; $tmp = $connection->id; break;
                case 3:$game_data->paddleT->id = $connection->id; $tmp = $connection->id; break;
                case 4:$game_data->paddleB->id = $connection->id; $tmp = $connection->id; break;
            }
            //var_dump($game_data);
            $propertyCheck = array(
                "type"=>"buttonJoin",
                "data" => $tmp
            );
            $connection->send(json_encode($propertyCheck));

        }else {
            $game_data = $data;
            $connection->send(sendGameData());
        }


    };

    // Emitted when connection closed
    $ws_worker->onClose = function ($connection) use ($ws_worker, &$clients, &$clientOrders) {
        global $game_data, $allCons, $start_who;

        $index = -1;
        for ($i = 0; $i < count($allCons); $i++) {
            if ($allCons[$i]->id == $connection->id) {
                $index = $i;
                break;
            }
        }

        array_splice($allCons, $index,1);

        switch ($connection->id % 4){
            case 1:
                $game_data->paddleL->player_name = null;
                //$newString = str_replace($game_data->paddleL->player_name,"",$game_data->paddleL->player_name);
                $game_data->paddleL->id = null;
                break;
            case 2:
                $game_data->paddleR->player_name = null;
                $game_data->paddleR->id = null;
                break;

            case 3:
                $game_data->paddleT->player_name = null;
                $game_data->paddleT->id = null;
                break;
            case 0:
                $game_data->paddleB->player_name = null;
                $game_data->paddleB->id = null;
                break;
        }



        /*
        $zapni = array(
            "type"=>"start"
        );

        echo "VYPINAM\n";

        var_dump($allIds);
        var_dump($allIds[0]);

        if (!empty($allIds)){
            echo "neni prazdny";
            $ws_worker->connections[$allIds[0]]->send(json_encode($zapni));
        }*/

        $game_data->num_players = $game_data->num_players - 1;

        if ($game_data->num_players < 0){
            $game_data->num_players = 0;
        }
        $zapni = array(
            "type"=>"start"
        );

        //var_dump($allCons);
        echo $connection->id;
        if (!empty($allCons)){
            if ($start_who == $connection->id){
                //echo "som v ife";
                $allCons[$index]->send(json_encode($zapni));
                $start_who = $allCons[$index]->id;
            }
        }
        $connection->send(sendGameData());

        echo "Connection closed\n";
    };
};
// Run worker
Worker::runAll();

?>
    