<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\worker\GenerateReportHandler;
use App\worker\SendEmailHandler;
use App\worker\Worker;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('task_queue', false, true, false, false);

$worker = new Worker($channel);
$worker->addHandler('send_email', new SendEmailHandler());
$worker->addHandler('generate_report', new GenerateReportHandler());

$worker->run();

$channel->close();
$connection->close();