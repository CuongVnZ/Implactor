<?php
/**
*
* 
*  _____                 _            _             
* |_   _|               | |          | |            
*   | |  _ __ ___  _ __ | | __ _  ___| |_ ___  _ __ 
*   | | | '_ ` _ \| '_ \| |/ _` |/ __| __/ _ \| '__|
*  _| |_| | | | | | |_) | | (_| | (__| || (_) | |   
* |_____|_| |_| |_| .__/|_|\__,_|\___|\__\___/|_|   
*                 | |                               
*                 |_|                               
*
* Implactor (c) 2018
* This plugin is licensed under GNU General Public License v3.0!
* It is free to use, copyleft license for software and other 
* kinds of works.
* ------===------
* > Author: Zadezter
* > Team: ImpladeDeveloped
*
*
**/
declare(strict_types=1);
namespace Implactor\tasks;

use pocketmine\level\{
	Level, Position
};
use pocketmine\{
        Player, Server
};
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket as JoinPacket;

use Implactor\Implade;

class GuardianJoinTask extends Task {

	private $player;
	private $plugin;

	public function __construct(Implade $plugin, Player $player){
        $this->plugin = $plugin;
        $this->player = $player;
	}
	
	public function onRun(int $currentTick): void{
		$player = $this->player;
		$packetJoin = new JoinPacket();
		$packetJoin->evid = JoinPacket::EVENT_GUARDIAN_CURSE;
		$packetJoin->data = 0;
		$packetJoin->position = $player->asVector3();
		$player->sendDataPacket($packetJoin);
	}
}
