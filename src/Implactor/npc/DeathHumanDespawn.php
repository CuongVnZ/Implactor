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
namespace Implactor\npc;

use Implactor\Implade;
use Implactor\npc\DeathHuman;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class DeathHumanDespawn extends Task {
	
    /** @var MainIR $plugin */
    private $plugin;
	/** @var Entity $entity */
	private $entity;
	/** @var Player $player */
	private $player;
	
	public function __construct(Implade $plugin, Entity $entity, Player $player){
        $this->plugin = $plugin;
		$this->entity = $entity;
		$this->player = $player;
	}
	
	public function onRun(int $currentTick) : void{
		$entity = $this->entity;
		if($entity instanceof DeathHuman){
			if($this->entity->getNameTag() === "§7[§cDeath§7]§r\n§f" .$this->player->getName(). "") $this->entity->close();
			$this->entity->close();
		}
	}
}
