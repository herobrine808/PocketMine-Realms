<?php

require_once(__DIR__ . '/EntityManager.php');

function getCaller($sid) {
	return EntityManager::get()->getRepository('Realms\Player')->findOneBy(array('sessionId' => $sid));
} 

?>