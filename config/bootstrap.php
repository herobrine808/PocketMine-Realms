<?php
require_once(dirname(__FILE__).'/../vendor/autoload.php');

//realms
require_once(dirname(__FILE__).'/../entities/Server.php');
require_once(dirname(__FILE__).'/../entities/Player.php');
require_once(dirname(__FILE__).'/../entities/Invite.php');

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$config = parse_ini_file(dirname(__FILE__) . '/./realms.properties', true);

$paths = array(dirname(__FILE__) . '/../entities');
$isDevMode = true;
$proxyDir = dirname(__FILE__) . '/../entities/proxies';

$dbParams = array(
    'driver'   => 'pdo_mysql',
    'host'		=> $config['database']['host'],
    'port'		=> $config['database']['port'],
    'user'     => $config['database']['username'],
    'password' => $config['database']['password'],
    'dbname'   => $config['database']['database'],
    'charset' => 'utf8'
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxyDir);
$entityManager = EntityManager::create($dbParams, $config);

?>