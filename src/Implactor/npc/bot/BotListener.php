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

use pocketmine\Player;
use pocketmine\math\{
        Vector2, AxisAlignedBB
};
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\{
	AnimatePacket, MovePlayerPacket, MoveEntityPacket
};
use pocketmine\event\entity\{
	EntitySpawnEvent, EntityDamageEvent, EntityDamageByEntityEvent
};
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as IR;

use Implactor\npc\bot\BotTask;
use Implactor\npc\bot\BotHuman;
use Implactor\Implade;

class BotListener implements Listener {

	private $plugin;

	public function __construct(Implade $plugin){
		$this->plugin = $plugin;
	}

	public function onEntitySpawn(EntitySpawnEvent $ev): void{
		$entity = $ev->getEntity();

		if($entity instanceof BotHuman){
			$this->plugin->getScheduler()->scheduleRepeatingTask(new BotTask($this->plugin, $entity), 200);
		}
	}

	public function onSwing(EntityDamageEvent $ev): void{
			$entity = $ev->getEntity();

			if($ev instanceof EntityDamageByEntityEvent){
				$damager = $ev->getDamager();

				if($entity instanceof BotHuman){
					$pk = new AnimatePacket();
					$pk->entityRuntimeId = $entity->getId();
					$pk->action = AnimatePacket::ACTION_SWING_ARM;
					$damager->dataPacket($pk);
				}
		}
	}

    public function onMove(PlayerMoveEvent $ev) : void{
    		$player = $ev->getPlayer();
    		$from = $ev->getFrom();
    		$to = $ev->getTo();
    		$distance = 7;

    		if($from->distance($to) < 0.1) return;
    		foreach($player->getLevel()->getNearbyEntities($player->getBoundingBox()->expandedCopy($distance, $distance, $distance), $player) as $entity){
    	
            if($entity instanceof BotHuman){
                $pk = new MoveEntityPacket();
                $v = new Vector2($entity->x, $entity->z);
                $yaw = ((atan2($player->z - $entity->z, $player->x - $entity->x) * 180) / M_PI) - 90;
            	$pitch = ((atan2($v->distance($player->x, $player->z), $player->y - $entity->y) * 180) / M_PI) - 90;
                $pk->entityRuntimeId = $entity->getId();
                $pk->position = $entity->asVector3()->add(0, 1.5, 0);
                $pk->yaw = $yaw;
                $pk->headYaw = ((atan2($player->z - $entity->z, $player->x - $entity->x) * 180) / M_PI) - 90;
                $pk->pitch = $pitch;
                $player->dataPacket($pk);
                $entity->setRotation($yaw, $pitch);
              }
           }
        }
    }
