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

use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\Location;
use Implactor\Implade;

class DeathParticles extends Task {
	
	/** @var Player */
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
		$deathone = new HugeExplodeParticle($center, $r, $g, $b, 1);
		for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
			$x = -sin($yaw) + $center->x;
			$z = cos($yaw) + $center->z;
			$deathone->setComponents($x, $y, $z);
			$death->addParticle($deathone);
		$r = rand(1,300);
		$g = rand(1,300);
		$b = rand(1,300);
		$x = $this->player->getX();
		$y = $this->player->getY();
		$z = $this->player->getZ();
		$center = new Vector3($x, $y, $z);
		$radius = 1;
		$count = 6;
		$deathtwo = new LavaParticle($center, $r, $g, $b, 1);
		for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
			$x = -sin($yaw) + $center->x;
			$z = cos($yaw) + $center->z;
			$deathtwo->setComponents($x, $y, $z);
			$death->addParticle($deathtwo);
		}
	}
}
