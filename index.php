<?php

require_once(__DIR__ . '/config/DependencyLoader.php');


// TODO all this needs to be finished!
$requestHandler->respond('GET', '/realms/m/login', function($request, $response, $service) {
	$service->render('pages/login.php', array());
});

$requestHandler->respond('GET', '/realms/m/register', function($request, $response, $service) {
	$service->render('pages/register.php', array());
});

// Below is pretty much ready

$requestHandler->respond('GET', '/realms/info/status', function($request) {
	return json_encode(array('buyServerEnabled' => false, 'createServerEnabled' => false));
});

$requestHandler->respond('GET', '/realms/server/list', function ($request, $response) {
	
	if(!isset($request->sid)) {
		$sid = $request->cookies()->get('sid');
		if($sid === null) {
			$response->code(401);
			$response->body('Session is required');
			$response->send();
			return;
		}
	} else {
		$sid = $request->sid;
	}
	
	
	$caller = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('sessionId' => $sid));
	if($caller === null) {
		$response->code(401);
		$response->body('Invalid session');
		$response->send();
		return;
	}
	
	$servers = array();
	foreach($caller->getInvitations() as $invite) {
		$servers[] = $invite->getServer();
	}
	
	foreach($caller->getServers() as $server) {
		$servers[] = $server;
	}

	$data = array();
	foreach($servers as $server) {
		$invited = array();
		foreach($server->getInvitations() as $invite) {
			$invited[] = $invite->getPlayer()->getName();
		}
		
		$players = array();
		foreach($server->getPlayers() as $player) {
			$players[] = $player->getName();
		}
		
		$data[] = array(
			'serverId' => $server->getServerId(),
			'name' => $server->getName(),
			'open' => $server->getOpen(),
			'ownerName' => $server->getOwner()->getName(),
			'myWorld' => ($server->getOwner()->getPlayerId() === $caller->getPlayerId()),
			'maxNrPlayers' => $server->getMaxPlayers(),
			'type' => $server->getType(),
			'playerNames' => $players,
			'invited' => $invited
		);
		
		unset($invited);
		unset($players);
			 
	}
    return json_encode($data);
});

$requestHandler->respond('POST', '/realms/server/[i:id]/join', function ($request, $response) {
	if(!isset($request->sid)) {
		$sid = $request->cookies()->get('sid');
		if($sid === null) {
			$response->code(401);
			$response->body('Session is required');
			$response->send();
			return;
		}
	} else {
		$sid = $request->sid;
	}
	
	
	$caller = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('sessionId' => $sid));
	if($caller === null) {
		$response->code(401);
		$response->body('Invalid session');
		$response->send();
		return;
	}

	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Invalid server');
		$response->send();
		return;
	}
	
	if(!$server->getIsOpen()) {
		$response->code(401);
		$response->body('Server closed');
		$response->send();
		return;
	}
	
	if($server->getOwner()->getPlayerId() !== $caller->getPlayerId()) {
		$invite = EntityManager::get()->getRepository('Realms\Invite')->findOneBy(array(
			'player' => $caller,
			'server' => $server
		));
		if($invite === null) {
			$response->code(401);
			$response->body('Not invited');
			$response->send();
			return;
		}
	}
	
	$data = array(
		'ip' => $server->getIp(),
		'port' => $server->getPort(),
		'key' => $server->getKey()
	);
	
	return json_encode($data);
	
	
		
});

$requestHandler->respond('POST', '/realms/server/create', function ($request, $response) {
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

$requestHandler->respond('PUT', '/realms/server/[i:id]/name/[a:name]', function($request, $response) {
	
	if(!isset($request->sid)) {
		$sid = $request->cookies()->get('sid');
		if($sid === null) {
			$response->code(401);
			$response->body('Session is required');
			$response->send();
			return;
		}
	} else {
		$sid = $request->sid;
	}
	
	
	$caller = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('sessionId' => $sid));
	if($caller === null) {
		$response->code(401);
		$response->body('Invalid session');
		$response->send();
		return;
	}
	
	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Invalid server');
		$response->send();
		return;
	}
	
	if($server->getOwner()->getPlayerId() !== $caller->getPlayerId()) {
		$response->code(401);
		$response->body('Not owner');
		$response->send();
		return;
	}
	
	$server->setName($request->name);
	
	EntityManager::get()->merge($server);
	EntityManager::get()->flush();
	
	return 'Renamed';
});

$requestHandler->respond('PUT', '/realms/server/[i:id]/open', function($request, $response) {
	if(!isset($request->sid)) {
		$sid = $request->cookies()->get('sid');
		if($sid === null) {
			$response->code(401);
			$response->body('Session is required');
			$response->send();
			return;
		}
	} else {
		$sid = $request->sid;
	}
	
	
	$caller = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('sessionId' => $sid));
	if($caller === null) {
		$response->code(401);
		$response->body('Invalid session');
		$response->send();
		return;
	}

	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Invalid server');
		$response->send();
		return;
	}
	
	if($server->getOwner()->getPlayerId() !== $caller->getPlayerId()) {
		$response->code(401);
		$response->body('Not owner');
		$response->send();
		return;
	}
	
	$server->setOpen(true);
	
	EntityManager::get()->merge($server);
	EntityManager::get()->flush();
	
	return 'Opened';
});

$requestHandler->respond('PUT', '/realms/server/[i:id]/close', function($request, $response) {
	if(!isset($request->sid)) {
		$sid = $request->cookies()->get('sid');
		if($sid === null) {
			$response->code(401);
			$response->body('Session is required');
			$response->send();
			return;
		}
	} else {
		$sid = $request->sid;
	}
	
	
	$caller = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('sessionId' => $sid));
	if($caller === null) {
		$response->code(401);
		$response->body('Invalid session');
		$response->send();
		return;
	}

	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Invalid server');
		$response->send();
		return;
	}
	
	if($server->getOwner()->getPlayerId() !== $caller->getPlayerId()) {
		$response->code(401);
		$response->body('Not owner');
		$response->send();
		return;
	}
	
	$server->setOpen(false);
	
	EntityManager::get()->merge($server);
	EntityManager::get()->flush();
	
	return 'Closed';
});

$requestHandler->respond('PUT', '/realms/server/[i:id]/whitelist/[a:name]', function($request, $response) {
	if(!isset($request->sid)) {
		$sid = $request->cookies()->get('sid');
		if($sid === null) {
			$response->code(401);
			$response->body('Session is required');
			$response->send();
			return;
		}
	} else {
		$sid = $request->sid;
	}
	
	
	$caller = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('sessionId' => $sid));
	if($caller === null) {
		$response->code(401);
		$response->body('Invalid session');
		$response->send();
		return;
	}
	
	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Invalid server');
		$response->send();
		return;
	}
	
	if($server->getOwner()->getPlayerId() !== $caller->getPlayerId()) {
		$response->code(401);
		$response->body('Not owner');
		$response->send();
		return;
	}
	
	$player = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('name' => $request->name));
	if($player === null) {
		$response->code(404);
		$response->body('Invalid player');
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

$requestHandler->respond('DELETE', '/realms/server/[i:id]/whitelist/[a:name]', function($request, $response) {
	if(!isset($request->sid)) {
		$sid = $request->cookies()->get('sid');
		if($sid === null) {
			$response->code(401);
			$response->body('Session is required');
			$response->send();
			return;
		}
	} else {
		$sid = $request->sid;
	}
	
	
	$caller = EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('sessionId' => $sid));
	if($caller === null) {
		$response->code(401);
		$response->body('Invalid session');
		$response->send();
		return;
	}
	
	$server = EntityManager::get()->find('Realms\Server', $request->id);
	if($server === null) {
		$response->code(404);
		$response->body('Server not found');
		$response->send();
		return;
	}
	
	if($server->getOwner()->getPlayerId() !== $caller->getPlayerId()) {
		$response->code(401);
		$response->body('Not owner');
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

$requestHandler->respond('POST', '/realms/server/heartbeat', function($request, $response) {
	if(!isset($request->nplayers)) {
		$response->code(400);
		$response->body('Bad request');
		$response->send();
		return;
	}
	
	$key = $request->cookies()->get('key');
	if($key === null) {
		$response->code(401);
		$response->body('key is required');
		$response->send();
		return;
	}
	
	$server = EntityManager::get()->getRepository('Realms\Server')->findOneBy(array('key' => $key));
	if($server === null) {
		$response->code(401);
		$response->body('Invalid key');
		$response->send();
		return;
	}
	
	//What should we do with it at this point?
	
});

// From server
$requestHandler->respond('GET', '/realms/auth/validate-player/[a:a]/[a:b]', function($request, $response) {
	//this probably checks the whitelist but what are the parameters?
});

$requestHandler->dispatch();

?>
