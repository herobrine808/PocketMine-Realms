<?php namespace Realms;
/**
 * @Entity @Table(name="realms_players")
 */
class Player {
	/** @Id @Column(type="integer") @GeneratedValue **/
	private $playerId;
	/** @Column(type="string", length=45, nullable=false, unique=true) **/
	private $name;
	/** @Column(type="string", length=45, nullable=false) **/
	public $sessionId;
	
	/**
 	 * @OneToMany(targetEntity="Realms\Server", mappedBy="owner")
 	 */
	public $servers;
	
	/**
	 * @OneToMany(targetEntity="Realms\Invite", mappedBy="player")
	 */
	public $invitations;
	
	/**
	 * @ManyToOne(targetEntity="Realms\Server", inversedBy="players")
	 * @JoinColumn(name="serverId", referencedColumnName="serverId")
	**/
	private $server;
	
	public function __construct() {
    		$this->servers = new \Doctrine\Common\Collections\ArrayCollection();
   		$this->invitations = new \Doctrine\Common\Collections\ArrayCollection();

	}
	
	public function getPlayerId() {
		return $this->playerId;
	}
	
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
		
	public function getSessionId() {
		return $this->sessionId;
	}
	public function setSessionId($sessionId) {
		$this->sessionId = $sessionId;
	}
	
	public function getServers() {
		return $this->servers;
	}
	
	public function getInvitations() {
		return $this->invitations;
	}
	
	public function getServer() {
		return $this->server;
	}
	public function setServer($server) {
		$this->server = $server;
	}
	
}
?>
