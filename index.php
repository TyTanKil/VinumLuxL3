<?php
session_start();

require_once('./Model/Connection.php');
$pdoBuilder = new Connection();
$db = $pdoBuilder->getDb();

if (
    (isset($_GET['ctrl']) && !empty($_GET['ctrl'])) &&
    (isset($_GET['action']) && !empty($_GET['action']))
) {
    $ctrl = $_GET['ctrl'];
    $action = $_GET['action'];
} else {
    $ctrl = 'category';
    $action = 'display';
}

$ctrl = ucfirst(strtolower($ctrl));
require_once("Controller/{$ctrl}Controller.php");

$ctrl = "{$ctrl}Controller";
$controller = new $ctrl($db);
$controller->$action();
