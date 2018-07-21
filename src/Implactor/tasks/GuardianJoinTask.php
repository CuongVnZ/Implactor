<?php
declare(strict_types=1);
namespace Implactor\tasks;

use pocketmine\level\{
	Level, Position
];
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

use Implactor\Implade;

class GuardianJoinTask extends Task {

	private $player;
	private $plugin;

	public function __construct(Main $plugin, Player $player){
                $this->plugin = $plugin;
                $this->player = $player;
	}
	
	public function onRun(int $currentTick): void{
		$player = $this->player;
		$pk = new LevelEventPacket();
		$pk->evid = LevelEventPacket::EVENT_GUARDIAN_CURSE;
		$pk->data = 0;
		$pk->position = $player->asVector3();
		$player->dataPacket($pk);
	}
}
