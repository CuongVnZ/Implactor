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

use pocketmine\level\{
	Level, Position
};
use pocketmine\level\particle\{
	FlameParticle as Flame, WaterParticle as Water
};
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

use Implactor\Implade;
use Implactor\npc\bot\{
	BotHuman, BotTask
};

class BotParticle extends Task {
	
	private $plugin, $entity;
	
	public function __construct(Implade $plugin, Entity $entity){
		$this->plugin = $plugin;
		$this->entity = $entity;
	}
	
	public function onRun(int $tick): void{
		$entity = $this->entity;
		if($entity instanceof BotHuman){
			$botparticle = $entity->getLevel();
			if($entity->isAlive()){
				for($yaw = 0; $yaw <= 10; $yaw += 0.5){
					$x = 0.5 * sin($yaw);
					$y = 0.5;
					$z = 0.5 * cos($yaw);
					$botparticle->addParticle(new Flame($entity->add($x, $y, $z)));
					$botparticle->addParticle(new Water($entity->add($x, $y, $z)));
				}
			}
		}
	}
}
