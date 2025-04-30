<?php

if (!isset($event)) {
    $event = "unknown";
}

$log = date("Y-m-d H:i:s") . ' | ' . file_get_contents('php://input');

file_put_contents("$event.log", $log . PHP_EOL, FILE_APPEND | LOCK_EX);

echo "ok";
