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
namespace Implactor\npc\bot;

use pocketmine\entity\Entity;
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;

use Implactor\Implade;
use Implactor\npc\bot\BotHuman;

class BotWalkingTask extends Task {

	private $plugin, $entity;

	public function __construct(Implade $plugin, Entity $entity){
		$this->plugin = $plugin;
		$this->entity = $entity;
	}

	public function onRun(int $tick): void{
			$entity = $this->entity;
			if($entity instanceof BotHuman){
				switch($entity->getDirection()){
				case 0:
				$entity->setMotion(new Vector3(0.8, 0, 0));
				break;
				case 1:
				$entity->setMotion(new Vector3(0, 0, 0.8));
				break;
				case 2:
				$entity->setMotion(new Vector3(-0.8, 0, 0));
				break;
				case 3:
				$entity->setMotion(new Vector3(0, 0, -0.8));
				break;
			}
		}
	}
}
