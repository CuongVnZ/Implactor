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
namespace Implactor\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;

use Implactor\Implade;

class AntiCaps implements Listener {

    private $plugin;
    private $caps;

    public function __construct(Implade $plugin){
        $this->plugin = $plugin;
        $this->caps = ["MOTHER", "FATHER", "HEY","HATE", "YOU", "IDIOT", "WHY", "STUPID", "WHAT", "WORST", "WOW", "AMAZING", "COOL", "WEIRDO", "FUN", "PUSSY", "PUSS", "GROSS", "SOLO", "TEAM", "PVP", "FRIEND", "ALLY", "FACTION", "GAMES", "SERVER", "DAMN", "DARN", "BUU", "TRASH", "ARE", "PRO", "NOOB", "REAL", "FAKE", "HI", "HEYO", "HELLO", "HOLA", "NO", "YES", "STOP", "BAN", "KICK"];
    }

    public function onChat(PlayerChatEvent $ev): void{
        $msg = $ev->getMessage();
        $player = $ev->getPlayer();
        if(!$player instanceof Player) return;
        if($player->hasPermission("implactor.anti")){
        }else{
            foreach($this->caps as $caps){
                if(strpos($msg, $caps) !== false){
                    $player->sendMessage("§l§8(§6!§8)§r §6Do not using a '§cCAPS§6' on this server!");
                    $ev->setCancelled();
                    return;
                }
            }
        }
    }
}
