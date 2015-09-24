<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/24
 * Time: 16:02
 */

date_default_timezone_set("PRC");
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$async = new \Jenner\Http\Async();
$task = \Jenner\Http\Task::createGet("http://www.baidu.com");
$task->registerHandler(function($info, $error, $content){
    echo "get baidu response. content length:" . strlen($content);
});
$async->attach($task, "baidu");

$task2 = \Jenner\Http\Task::createGet("http://www.sina.com");
$async->attach($task2, "sina");
$task->registerHandler(function($info, $error, $content){
    echo "get sina response. content length:" . strlen($content);
});

$task3 = \Jenner\Http\Task::createGet("http://www.qq.com");
$async->attach($task3, "qq");
$task->registerHandler(function($info, $error, $content){
    echo "get qq response. content length:" . strlen($content);
});

$result = $async->execute();
echo count($result);