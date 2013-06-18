<?php

require_once(__DIR__ . '/config/DependencyLoader.php');


$klein = new \Klein\Klein();

$klein->respond('GET', '/realms/m/login', function($request, $response, $service) {
	$service->render('pages/login.php', array());
});

$klein->respond('POST', '/realms/player/add', function($request, $response) {
	//Should have player and sessionId
	// Cookies should contain key to verify auth server
	//TODO Implement this
	return json_encode(array());	
});

$klein->respond('GET', '/realms/info/status', function($request) {
	return json_encode(array('buyServerEnabled' => false, 'createServerEnabled' => false));
});

//TODO list only whitelisted?
//TODO get player?
$klein->respond('GET', '/realms/server/list', function ($request) {
	$servers = EntityManager::get()->getRepository('Realms\Server')->findAll();
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

	$server = EntityManager::get()->find('Realms\Server', $request->id);
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
	$server->setOwner(EntityManager::get()->find('Realms\Player', 2));
	$server->setIp('10.10.10.10');
	$server->setPort(19132);
	
	EntityManager::get()->persist($server);
	EntityManager::get()->flush();
	
	return '{"success":true}';
	
	
});

// From client
$klein->respond('PUT', '/realms/server/[i:id]/name/[a:name]', function($request, $response) {
	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$server->setName($request->name);
	
	EntityManager::get()->merge($server);
	EntityManager::get()->flush();
	
	return '{"success":true}';
});

// From client?
// Check owner
$klein->respond('PUT', '/realms/server/[i:id]/open', function($request, $response) {
	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$server->setOpen(true);
	
	EntityManager::get()->merge($server);
	EntityManager::get()->flush();
	
	return '{"success":true}';
});

// From client?
// Check owner
$klein->respond('PUT', '/realms/server/[i:id]/close', function($request, $response) {
	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$server->setOpen(false);
	
	EntityManager::get()->merge($server);
	EntityManager::get()->flush();
	
	return '{"success":true}';
});

// From client
$klein->respond('PUT', '/realms/server/[i:id]/whitelist/[a:name]', function($request, $response) {
	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$player = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('name' => $request->name));
	if($player === null) {
		$response->code(404);
		$response->body('Player not found');
		$response->send();
		return;
	}
	
	$invite = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('server' => $server, 'player' => $player));
	if($invite === null) {
		$invite = new Realms\Invite();
		$invite->setServer($server);
		$invite->setPlayer($player);
		EntityManager::get()->persist($invite);
		EntityManager::get()->flush();
	}
	
	$response->code(200);
	$response->body('Player invited');
	$response->send();
	return;
});

// From client
$klein->respond('DELETE', '/realms/server/[i:id]/whitelist/[a:name]', function($request, $response) {
	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	$player = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('name' => $request->name));
	if($player === null) {
		$response->code(404);
		$response->body('Player not found');
		$response->send();
		return;
	}
	
	$invite = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('server' => $server, 'player' => $player));
	if($invite === null) {
		$response->code(404);
		$response->body('Not invited');
		$response->send();
		return;
	}
	
	EntityManager::get()->remove($invite);
	EntityManager::get()->flush();
	
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

?>
