<?php

declare(strict_types = 1);

require("src/errYexp/ErrorHandle.php");
set_exception_handler("ErrorHandler::handleException");
set_error_handler("ErrorHandler::handleError");
require("src/Database.php");
require("src/controllers/SociosController.php");
require("src/controllers/PistasController.php");
require("src/controllers/ReservasController.php");
require("src/models/PistasGateway.php");
require("src/models/ReservasGateway.php");
require("src/models/SociosGateway.php");


$database = new Database("localhost" , "deportes_db" , "root" , "");


header("Content-Type: application/json; charset=utf-8");

$partes = explode("/" , $_SERVER["REQUEST_URI"]);

$endpoint =  $partes[2];

$id = $partes[3] ?? null;

$method = $_SERVER["REQUEST_METHOD"];

switch($endpoint){
    case "socios":
        $sociosGateway = new SociosGateway($database);
        $socioscontroller = new SociosController($sociosGateway);
        $socioscontroller->processRequest($method,$id);
        break;

    case "pistas":
        $pistasGateway = new PistasGateway($database);
        $pistascontroller = new PistasController($pistasGateway);
        $pistascontroller->processRequest($method,$id);
        break;


    case "reservas":
        $reservaGateway = new ReservasGateway($database);
        $reservascontroller = new ReservasController($reservaGateway);
        $reservascontroller->processRequest($method,$id);
        break;

    default:
    http_response_code(404);
    break;

}