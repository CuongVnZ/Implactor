<?php
declare(strict_types=1);
namespace Implactor\tasks;

use pocketmine\level\{
	Level, Position
};
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

use Implactor\Implade;

class TotemRespawnTask extends Task {

	private $player;
	private $plugin;

	public function __construct(Main $plugin, Player $player){
                $this->plugin = $plugin;
                $this->player = $player;
	}
	
	public function onRun(int $currentTick): void{
		$pk = new LevelEventPacket();
		$pk->evid = LevelEventPacket::EVENT_SOUND_TOTEM;
		$pk->data = 0;
		$pk->position = $this->player->asVector3();
		$this->player->dataPacket($pk);
	}
}
