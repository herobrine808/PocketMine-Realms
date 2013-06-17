<?php namespace Realms;
/**
 * @Entity @Table(name="realms_servers")
 */
class Server {
	/** @Id @Column(type="integer") @GeneratedValue **/
	private $serverId;
	/** @Column(type="string", length=45, nullable=false, unique=true) **/
	private $name;
	/** @Column(type="string", length=15, nullable=false) **/
	public $ip;
	/** @Column(type="integer", nullable=false) **/
	public $port;
	/** @Column(type="boolean", nullable=false) **/
	public $open;
	/** @Column(type="string", nullable=false) **/
	public $type;
	/** @Column(type="string", length=128, nullable=false) **/
	public $seed;
	/** @Column(type="string", nullable=false) **/
	public $key;
	/** @Column(type="integer", nullable=false) **/
	public $maxPlayers;
	
	/**
     * @ManyToOne(targetEntity="Realms\Player", inversedBy="servers")
     * @JoinColumn(name="ownerId", referencedColumnName="playerId")
     **/
    private $owner;
    
    /**
	 * @OneToMany(targetEntity="Realms\Invite", mappedBy="server")
	 */
	public $invitations;
	
	/**
 	 * @OneToMany(targetEntity="Realms\Player", mappedBy="server")
 	 */
	public $players;

	public function __construct() {
		$this->invitations = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->players = new \Doctrine\Common\Collections\ArrayCollection();


        $this->port = 19132;
        $this->open = true;
        $this->type = 'creative';
        $this->key = '??xxxx??';
        $this->maxPlayers = 20;
    }
	
	public function getServerId() {
		return $this->serverId;
	}
	
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
		
	public function getIp() {
		return $this->ip;
	}
	public function setIp($ip) {
		$this->ip = $ip;
	}
	
	public function getPort() {
		return $this->port;
	}
	public function setPort($port) {
		$this->port = $port;
	}
	
	public function getOpen() {
		return $this->open;
	}
	public function setOpen($open) {
		$this->open = $open;
	}
	
	public function getType() {
		return $this->type;
	}
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getSeed() {
		return $this->seed;
	}
	public function setSeed($seed) {
		$this->seed = $seed;
	}
	
	public function getKey() {
		return $this->key;
	}
	public function setKey($key) {
		$this->key = $key;
	}
	
	public function getMaxPlayers() {
		return $this->maxPlayers;
	}
	public function setMaxPlayers($maxPlayers) {
		$this->maxPlayers = $maxPlayers;
	}
	
	public function getOwner() {
		return $this->owner;
	}
	public function setOwner($owner) {
		$this->owner = $owner;
	}
	
	public function getInvitations() {
		return $this->invitations;
	}
	
	public function getPlayers() {
		return $this->players;
	}
	
}
?>