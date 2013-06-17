<?php namespace Realms;
/**
 * @Entity @Table(name="realms_invites")
 */
class Invite {
	/** @Id @Column(type="integer") @GeneratedValue **/
	private $inviteId;
	/** @Column(type="boolean", nullable=false) **/
	private $accepted;
	
	/**
     * @ManyToOne(targetEntity="Realms\Server", inversedBy="invitations")
     * @JoinColumn(name="serverId", referencedColumnName="serverId")
     **/
    private $server;
	/**
     * @ManyToOne(targetEntity="Realms\Player", inversedBy="invitations")
     * @JoinColumn(name="playerId", referencedColumnName="playerId")
     **/
    private $player;
	
	public function __construct() {
		$this->accepted = false;
    }
	
	public function getInviteId() {
		return $this->inviteId;
	}
	
	public function getAccepted() {
		return $this->accepted;
	}
	public function setAccepted($accepted) {
		$this->accepted = $accepted;
	}
	
	public function getServer() {
		return $this->server;
	}
	public function setServer($server) {
		$this->server = $server;
	}
	
	public function getPlayer() {
		return $this->player;
	}
	public function setPlayer($player) {
		$this->player = $player;
	}
	
}
?>