<?php

require_once(__DIR__ . '/bootstrap.php');

class EntityManager {
	
	private $em;

	private static $instance = null;
	
	public static function init($em) {
		self::$instance = new EntityManager($em);
	}

	public static function get()
    {
        return self::$instance->em;
    }

	protected function __construct($em) {
		$this->em = $em;
	}
}

EntityManager::init($entityManager);

?>