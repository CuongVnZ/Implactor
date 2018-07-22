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
namespace Implactor\tridents;

use pocketmine\{
	Player, Server
};
use pocketmine\network\mcpe\protocol\{
	PlaySoundPacket as TridentSound, TakeItemEntityPacket as TakeTridentItem
};
use pocketmine\block\Block;
use pocketmine\item\Item as Rare;
use pocketmine\level\Level;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile as TridentProjectile;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\math\RayTraceResult;

class ThrownTrident extends TridentProjectile {
	
	public const NETWORK_ID = self::TRIDENT;
	public $height = 0.35;
	public $width = 0.25;
	public $gravity = 0.10;
	protected $damage = 6;

	public function __construct(Level $level, CompoundTag $nbt, ?Entity $shootingEntity = \null){
          parent::__construct($level, $nbt, $shootingEntity);
	}

	public function onCollideWithPlayer(Player $player): void{
		if($this->blockHit === \null){
		return;
		}
		$tridentItem = Rare::nbtDeserialize($this->namedtag->getCompoundTag(Trident::TRIDENT_WEAPON));
		$tridentOnInventory = $player->getInventory();
		if($player->isSurvival() and !$tridentOnInventory->canAddItem($tridentItem)){
		return;
		}
		$pk = new TakeTridentItem();
		$pk->eid = $player->getId();
		$pk->target = $this->getId();
		$this->server->broadcastPacket($this->getViewers(), $pk);
		if(!$player->isCreative()){
		$tridentOnInventory->addItem(clone $tridentItem);
		}
		$this->flagForDespawn();
	}

	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
		if($entityHit === $this->getOwningEntity()){
		return;
		}
		parent::onHitEntity($entityHit, $hitResult);
		$pk = new TridentSound();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->soundName = "item.trident.hit";
		$pk->volume = 1;
		$pk->pitch = 1;
		$this->server->broadcastPacket($this->getViewers(), $pk);
	}

	public function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
		parent::onHitBlock($blockHit, $hitResult);
		$pk = new TridentSound();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->soundName = "item.trident.hit_ground";
		$pk->volume = 1;
		$pk->pitch = 1;
		$this->server->broadcastPacket($this->getViewers(), $pk);
	}
}
