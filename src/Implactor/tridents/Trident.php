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
declare(strict_types = 1);
namespace Implactor\tridents;

use pocketmine\entity\Entity;
use pocketmine\item\Tool as Weapon;
use pocketmine\Player;

use Implactor\tridents\{
	ThrownTrident, TridentEntityManager, TridentItemManager
};

class Trident extends Weapon {

	public const TRIDENT_ITEM = "Trident";

	public function __construct($meta = 0, $count = 1){
          parent::__construct(self::TRIDENT, $meta, "Trident");
	}

	public function getMaxDurability(): int{
          return 251;
	}

	public function onReleaseUsing(Player $player): bool{
		if($player->getItemUseDuration() < 10){
			return false;
		}
		$tridentNBT = Entity::createBaseNBT(
			$player->add(0, $player->getEyeHeight(), 0),
			$player->getDirectionVector()->multiply(4),
			($player->yaw > 180 ? 360 : 0) - $player->yaw, -$player->pitch
		);
		$powerNBT = $this->nbtSerialize();
		$powerNBT->setName(self::TRIDENT_ITEM);
		$tridentNBT->setTag($powerNBT);
		if($player->isSurvival()){
		$this->tridentDamage(1);
		}
		$entity = Entity::createEntity("Thrown Trident", $player->getLevel(), $tridentNBT, $player, $this);
		$entity->spawnToAll();
		if($player->isSurvival()){
		$player->getInventory()->removeItem(clone $this);
		}
		return true;
	}

	public function getMaxStackSize(): int{
		return 1;
	}

	public function onAttackEntity(Entity $victim): bool{
		return $this->tridentDamage(1);
	}

	public function getAttackPoints(): int{
		return 6;
	}
}
