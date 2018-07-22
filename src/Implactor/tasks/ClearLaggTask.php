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
namespace Implactor\tasks;

use pocketmine\Server;
use pocketmine\scheduler\Task;

use Implactor\Implade;

class ClearLaggTask extends Task {
	
    private $plugin;	
	
    public function __construct(Implade $plugin){
        $this->plugin = $plugin;
    }
    
    public function onRun(int $currentTick) : void{
            $this->plugin->clearDroppedItems();
            $this->plugin->clearSpawnedMobs();
            $this->plugin->getServer()->broadcastMessage("§8§l(§a!§8)§r §aAll entities has cleared!"); 
	    }
    }
