CREATE TABLE `realms_invites` (
  `inviteId` bigint(20) unsigned NOT NULL auto_increment,
  `serverId` bigint(20) unsigned NOT NULL,
  `playerId` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`inviteId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `realms_players` (
  `playerId` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `sessionId` varchar(45) NOT NULL,
  `serverId` bigint(20) unsigned default NULL,
  PRIMARY KEY  (`playerId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE `realms_servers` (
  `serverId` bigint(20) unsigned NOT NULL auto_increment,
  `ownerId` bigint(20) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `port` smallint(5) unsigned NOT NULL default '19132',
  `open` tinyint(1) NOT NULL default '1',
  `type` varchar(10) NOT NULL default 'creative',
  `seed` varchar(128) NOT NULL default 'seed',
  `key` varchar(45) NOT NULL,
  `maxPlayers` int(10) unsigned NOT NULL default '20',
  PRIMARY KEY  (`serverId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


-- Foreign keys
ALTER TABLE `realms_invites` ADD KEY `fk_rinvites_server_id_idx` (`serverId`);
ALTER TABLE `realms_invites` ADD KEY `fk_rinvites_player_id_idx` (`playerId`);
ALTER TABLE `realms_invites` ADD CONSTRAINT `fk_rinvites_player_id` FOREIGN KEY (`playerId`) 
	REFERENCES `realms_players` (`playerId`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `realms_invites` ADD CONSTRAINT `fk_rinvites_server_id` FOREIGN KEY (`serverId`) 
	REFERENCES `realms_servers` (`serverId`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `realms_players` ADD KEY `fk_rplayers_server_id_idx` (`serverId`);
ALTER TABLE `realms_players` ADD CONSTRAINT `fk_rplayers_server_id` FOREIGN KEY (`serverId`) 
	REFERENCES `realms_servers` (`serverId`) ON DELETE SET NULL ON UPDATE SET NULL;


ALTER TABLE `realms_servers` ADD KEY `fk_rservers_owner_id_idx` (`ownerId`);
ALTER TABLE `realms_servers` ADD CONSTRAINT `fk_rservers_owner_id` FOREIGN KEY (`ownerId`) 
REFERENCES `realms_players` (`playerId`) ON DELETE CASCADE ON UPDATE CASCADE;
