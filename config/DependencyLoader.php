<?php

// Entity Manager
require_once(__DIR__ . '/EntityManager.php');
require_once(__DIR__ . '/Utils.php');

// Entities
require_once(__DIR__ . '/../entities/Server.php');
require_once(__DIR__ . '/../entities/Player.php');
require_once(__DIR__ . '/../entities/Invite.php');

// Klein
require_once(__DIR__ . '/../vendor/Klein/Klein.php');
require_once(__DIR__ . '/../vendor/Klein/ServiceProvider.php');
require_once(__DIR__ . '/../vendor/Klein/App.php');
require_once(__DIR__ . '/../vendor/Klein/Request.php');
require_once(__DIR__ . '/../vendor/Klein/Response.php');
require_once(__DIR__ . '/../vendor/Klein/ResponseCookie.php');
require_once(__DIR__ . '/../vendor/Klein/HttpStatus.php');
require_once(__DIR__ . '/../vendor/Klein/Validator.php');

require_once(__DIR__ . '/../vendor/Klein/DataCollection/DataCollection.php');
require_once(__DIR__ . '/../vendor/Klein/DataCollection/ServerDataCollection.php');
require_once(__DIR__ . '/../vendor/Klein/DataCollection/HeaderDataCollection.php');
require_once(__DIR__ . '/../vendor/Klein/DataCollection/ResponseCookieDataCollection.php');

require_once(__DIR__ . '/../vendor/Klein/Exceptions/KleinExceptionInterface.php');
require_once(__DIR__ . '/../vendor/Klein/Exceptions/DuplicateServiceException.php');
require_once(__DIR__ . '/../vendor/Klein/Exceptions/LockedResponseException.php');
require_once(__DIR__ . '/../vendor/Klein/Exceptions/ResponseAlreadySentException.php');
require_once(__DIR__ . '/../vendor/Klein/Exceptions/UnhandledException.php');
require_once(__DIR__ . '/../vendor/Klein/Exceptions/UnknownServiceException.php');
require_once(__DIR__ . '/../vendor/Klein/Exceptions/ValidationException.php');

$requestHandler = new \Klein\Klein();


?>
