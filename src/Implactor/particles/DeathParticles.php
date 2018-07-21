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
namespace Implactor\particles;

use pocketmine\level\{
	Level, Location, Position
};
use pocketmine\level\particle\{
	HugeExplodeParticle as BigExplosion, LavaParticle as LavaExplosion
};
use pocketmine\{
	Player, Server
};
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;

use Implactor\Implade;

class DeathParticles extends Task {
	
	private $player;
	
	public function __construct(Implade $plugin, Player $player){
		$this->player = $player;
	}
	
	public function onRun(int $currentTick): void{
		$death = $this->player->getLevel();
		$r = rand(1,300);
		$g = rand(1,300);
		$b = rand(1,300);
		$x = $this->player->getX();
		$y = $this->player->getY();
		$z = $this->player->getZ();
		$center = new Vector3($x, $y, $z);
		$radius = 1;
		$count = 6;
		$deathexplosion = new BigExplosion($center, $r, $g, $b, 1);
		for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
			$x = -sin($yaw) + $center->x;
			$z = cos($yaw) + $center->z;
			$deathexplosion->setComponents($x, $y, $z);
			$death->addParticle($deathexplosion);
        }
		$deathlava = new LavaExplosion($center, $r, $g, $b, 1);
		for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
			$x = -sin($yaw) + $center->x;
			$z = cos($yaw) + $center->z;
			$deathlava->setComponents($x, $y, $z);
			$death->addParticle($deathlava);
		}
	}
}
