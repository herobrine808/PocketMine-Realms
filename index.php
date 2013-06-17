<?php

require_once(dirname(__FILE__) . '/../bootstrap.php');

require_once(dirname(__FILE__) . '/../vendor/Klein/Klein.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/ServiceProvider.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/App.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Request.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Response.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/ResponseCookie.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/HttpStatus.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Validator.php');


require_once(dirname(__FILE__) . '/../vendor/Klein/DataCollection/DataCollection.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/DataCollection/ServerDataCollection.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/DataCollection/HeaderDataCollection.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/DataCollection/ResponseCookieDataCollection.php');

require_once(dirname(__FILE__) . '/../vendor/Klein/Exceptions/KleinExceptionInterface.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Exceptions/DuplicateServiceException.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Exceptions/LockedResponseException.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Exceptions/ResponseAlreadySentException.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Exceptions/UnhandledException.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Exceptions/UnknownServiceException.php');
require_once(dirname(__FILE__) . '/../vendor/Klein/Exceptions/ValidationException.php');



class EntityManager {
	public $em;

	private static $instance = null;
	public static function StartInstance($em) {
		if(self::$instance === null) {
			self::$instance = new EntityManager($em);
		}
	}

	public static function Instance()
    {
        return self::$instance;
    }

	protected function __construct($em) {
		$this->em = $em;
	}
}

EntityManager::StartInstance($entityManager);

$klein = new \Klein\Klein();

$klein->respond('GET', '/realms/info/status', function($request) {
	return json_encode(array('buyServerEnabled' => false, 'createServerEnabled' => false));
});

//TODO list only whitelisted?
//TODO get player?
$klein->respond('GET', '/realms/server/list', function ($request) {
	$servers = EntityManager::Instance()->em->getRepository('Realms\Server')->findAll();
	$response = array();
	foreach($servers as $server) {
		$invited = array();
		foreach($server->getInvitations() as $invite) {
			$invited[] = $invite->getPlayer()->getName();
		}
		
		$players = array();
		foreach($server->getPlayers() as $player) {
			$players[] = $player->getName();
		}
		
		$response[] = array(
			'serverId' => $server->getServerId(),
			'name' => $server->getName(),
			'open' => $server->getOpen(),
			'ownerName' => $server->getOwner()->getName(),
			'myWorld' => false,
			'maxNrPlayers' => $server->getMaxPlayers(),
			'type' => $server->getType(),
			'playerNames' => $players,
			'invited' => $invited
		);
		
		unset($invited);
			 
	}
    return json_encode($response);
});

$klein->respond('POST', '/realms/server/[i:id]/join', function ($request, $response) {
	//401 Session is required or Invalid session id or Not invited

	$server = EntityManager::Instance()->em->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	// Validate against whitelist
	
	$data = array(
		'ip' => $server->getIp(),
		'port' => $server->getPort(),
		'key' => $server->getKey()
	);
	
	return json_encode($data);
});

//Set extra info?
$klein->respond('POST', '/realms/server/create', function ($request, $response) {
	if(!isset($request->name) || !isset($request->type) || !isset($request->seed)) {
		$response->code(400);
		$response->body('Bad request');
		$response->send();
		return;
	}
	
	$server = new Realms\Server();
	$server->setName($request->name);
	$server->setType($request->type);
	$server->setSeed($request->seed);
	$server->setOwner(EntityManager::Instance()->em->find('Realms\Player', 2));
	$server->setIp('10.10.10.10');
	$server->setPort(19132);
	
	EntityManager::Instance()->em->persist($server);
	EntityManager::Instance()->em->flush();
	
	return '{"success":true}';
	
	
});

// From client
$klein->respond('PUT', '/realms/server/[i:id]/name/[a:name]', function($request, $response) {
	$server = EntityManager::Instance()->em->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$server->setName($request->name);
	
	EntityManager::Instance()->em->merge($server);
	EntityManager::Instance()->em->flush();
	
	return '{"success":true}';
});

// From client?
// Check owner
$klein->respond('PUT', '/realms/server/[i:id]/open', function($request, $response) {
	$server = EntityManager::Instance()->em->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$server->setOpen(true);
	
	EntityManager::Instance()->em->merge($server);
	EntityManager::Instance()->em->flush();
	
	return '{"success":true}';
});

// From client?
// Check owner
$klein->respond('PUT', '/realms/server/[i:id]/close', function($request, $response) {
	$server = EntityManager::Instance()->em->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$server->setOpen(false);
	
	EntityManager::Instance()->em->merge($server);
	EntityManager::Instance()->em->flush();
	
	return '{"success":true}';
});

// From client
$klein->respond('PUT', '/realms/server/[i:id]/whitelist/[a:name]', function($request, $response) {
	$server = EntityManager::Instance()->em->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$player = EntityManager::Instance()->em->getRepository('Realms\Player')->findOneBy(array('name' => $request->name));
	if($player === null) {
		$response->code(404);
		$response->body('Player not found');
		$response->send();
		return;
	}
	
	$invite = EntityManager::Instance()->em->getRepository('Realms\Player')->findOneBy(array('server' => $server, 'player' => $player));
	if($invite === null) {
		$invite = new Realms\Invite();
		$invite->setServer($server);
		$invite->setPlayer($player);
		EntityManager::Instance()->em->persist($invite);
		EntityManager::Instance()->em->flush();
	}
	
	$response->code(200);
	$response->body('Player invited');
	$response->send();
	return;
});

// From client
$klein->respond('DELETE', '/realms/server/[i:id]/whitelist/[a:name]', function($request, $response) {
	$server = EntityManager::Instance()->em->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$player = EntityManager::Instance()->em->getRepository('Realms\Player')->findOneBy(array('name' => $request->name));
	if($player === null) {
		$response->code(404);
		$response->body('Player not found');
		$response->send();
		return;
	}
	
	$invite = EntityManager::Instance()->em->getRepository('Realms\Player')->findOneBy(array('server' => $server, 'player' => $player));
	if($invite === null) {
		$response->code(404);
		$response->body('Not invited');
		$response->send();
		return;
	}
	
	EntityManager::Instance()->em->remove($invite);
	EntityManager::Instance()->em->flush();
	
	$response->code(200);
	$response->body('Player invited');
	$response->send();
	return;
});

// From server
$klein->respond('POST', '/realms/server/heartbeat', function($request, $response) {
	if(!isset($request->nplayers)) {
		$response->code(400);
		$response->body('Bad request');
		$response->send();
		return;
	}
	
	//server identifier?
});

// From server
$klein->respond('GET', '/realms/auth/validate-player/[a:a]/[a:b]', function($request, $response) {
	//this probably checks the whitelist but what are the parameters?
});

$klein->dispatch();

//echo 'Hello';

?>