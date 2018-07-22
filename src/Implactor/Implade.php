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
namespace Implactor;

use pocketmine\{
	Player, Server
};
use pocketmine\plugin\{
	Plugin, PluginBase, PluginDescription
};
use pocketmine\nbt\tag\{
	CompoundTag, ListTag, DoubleTag, FloatTag, NamedTag
};
use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\level\{
	Level, Position
};
use pocketmine\entity\{
	Entity, Effect, EffectInstance, Creature, Human
};
use pocketmine\level\sound\{
	EndermanTeleportSound as Join, BlazeShootSound as Quit, GhastSound as DeathOne, AnvilBreakSound as DeathTwo, DoorBumpSound as Bot, FizzSound as Book
};
use pocketmine\event\entity\{
	EntitySpawnEvent, EntityDamageEvent, EntityDamageByEntityEvent
};
use pocketmine\event\player\{
	PrePlayerLoginEvent, PlayerLoginEvent, PlayerJoinEvent, PlayerQuitEvent, PlayerDeathEvent, PlayerRespawnEvent, PlayerChatEvent
};
use pocketmine\level\particle\DestroyBlockParticle as Bloodful;
use pocketmine\event\Listener;
use pocketmine\block\Block;
use pocketmine\nbt\NBT;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use jojoe77777\FormAPI; // Two UI commands are required this plugin... :3

use Implactor\listeners\{
	AntiAdvertising, AntiSwearing, AntiCaps
};
use Implactor\tasks\{
	ChatCooldownTask, ClearLaggTask, GuardianJoinTask, TotemRespawnTask
};
use Implactor\tridents\{
	Trident, ThrownTrident, TridentEntityManager, TridentItemManager
};
use Implactor\particles\{
	SpawnParticles, DeathParticles
};
use Implactor\npc\{
	DeathHuman, DeathHumanDespawn
};
use Implactor\npc\bot\{
	BotHuman, BotListener, BotTask
};

class Implade extends PluginBase implements Listener {
	
        public $wild = [];
        public $ichat = [];
        private $visibility = [];
        
        public function onLoad(): void{
        	$this->getLogger()->info("Loading all codes and scanning the errors on Implactor...");
        	$this->getLogger()->notice("Checking the update...");
            try{
                if(($version = (new PluginDescription(file_get_contents("https://raw.githubusercontent.com/ImpladeDeveloped/Implactor/Implade/plugin.yml")))->getVersion()) != $this->getDescription()->getVersion()){
                    $this->getLogger()->notice("New version $version is now available! Update it on Github or Poggit!");
                }else{
                    $this->getLogger()->info("Implactor is already updated to the latest version!");
                }
            }catch(\Exception $ex){
                $this->getLogger()->warning("Unable to checking the update!");
             }
       }
 
        public function onEnable(): void{
        	$this->getLogger()->info("Implactor is currently now online! Thanks for using this plugin!");
            $this->getScheduler()->scheduleRepeatingTask(new SpawnParticles($this, $this), 15);
            $this->registerTridents();
            //* Events *//
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
		    $this->getServer()->getPluginManager()->registerEvents(new AntiAdvertising($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new AntiSwearing($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new AntiCaps($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new BotListener($this), $this);
            //* Entities *//
            Entity::registerEntity(DeathHuman::class, true);
		    Entity::registerEntity(BotHuman::class, true);
		   //* Clear Lagg *//
		    if(is_numeric(240)){ 
                $this->getScheduler()->scheduleRepeatingTask(new ClearLaggTask($this, $this), 240 * 20);
                }
      }
      
      public function checkDepends(): void{
          $this->formplugin = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
           if(is_null($this->formplugin)){
             $this->getLogger()->warning("FormAPI not found in plugin folder! Disabled two UI commands...");
            }
      }
    
       public function onDisable(): void{
       	$this->getLogger()->notice("Oh no, Implactor has self-destructed it's system!");
      }
      
      private function registerTridents(){
      	$this->getLogger()->notice("A mysterious legendary of Trident is loading and registering to Implactor!");
           TridentEntityManager::init();
           TridentItemManager::init();
      }
      
       public function onPreLogin(PlayerPreLoginEvent $ev) : void{
       	$player = $ev->getPlayer();
            if(!$this->getServer()->isWhitelisted($player->getName())){
			   $ev->setKickMessage("§l§7[§cMAINTENANCE§7]\n §eThis server is currently on maintenance mode!\n§eSorry for inconvience and please be patient!");
			   $ev->setCancelled(true);
			}
	   }
	
	    public function onLogin(PlayerLoginEvent $ev): void{
		    $player = $ev->getPlayer();
		    $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
	  }
	
		public function onJoin(PlayerJoinEvent $ev): void{
			$player = $ev->getPlayer();
	        $player->setGamemode(Player::SURVIVAL);
	        $this->getScheduler()->scheduleDelayedTask(new GuardianJoinTask($this, $player), 35);
			$player->sendMessage("§7[§aI§6R§7]§r §bThis server is running the Implactor!");
		if($player->isOP()){
			$ev->setJoinMessage("§7(§6STAFF§7) §l§8[§a+§8]§r §a{$player->getName()}");
			$player->getLevel()->addSound(new Join($player));
         }else{
			$ev->setJoinMessage("§l§8[§a+§8]§r §a{$player->getName()}");
			$player->getLevel()->addSound(new Join($player));
			}
	  }
	
		public function onQuit(PlayerQuitEvent $ev): void{
			$player = $ev->getPlayer();
	   if($player->isOP()){
			$ev->setQuitMessage("§7(§6STAFF§7) §l§8[§c-§8]§r §c{$player->getName()}");
			$player->getLevel()->addSound(new Quit($player));
         }else{
			$ev->setQuitMessage("§l§8[§c-§8]§r §c{$player->getName()}");
			$player->getLevel()->addSound(new Quit($player));
			}
	  }
	
		public function onRespawn(PlayerRespawnEvent $ev): void{
			$player = $ev->getPlayer();
			$player->setHealth(20);
			$this->getScheduler()->scheduleDelayedTask(new TotemRespawnTask($this, $player), 35);
	        $player->addTitle("§l§cYOU ARE DEAD", "§fOuch, what just happend?");
		    $player->setGamemode(Player::SURVIVAL);
			
	  }
	
		public function onDeath(PlayerDeathEvent $ev): void{
			$player = $ev->getPlayer();
			$player->getLevel()->addSound(new DeathOne($player));
		    $player->getLevel()->addSound(new DeathTwo($player));
		    $this->getScheduler()->scheduleDelayedTask(new DeathParticles($this, $player), 1);
		    $deathnbt = new CompoundTag("", [
			   new ListTag("Pos", [
				   new DoubleTag("", $player->getX()),
				   new DoubleTag("", $player->getY() - 1),
				   new DoubleTag("", $player->getZ())
			   ]),
			   new ListTag("Motion", [
				   new DoubleTag("", 0),
				   new DoubleTag("", 0),
				   new DoubleTag("", 0)
			   ]),
			   new ListTag("Rotation", [
				   new FloatTag("", 2),
				   new FloatTag("", 2)
			   ])
		   ]);
		   $deathnbt->setTag($player->namedtag->getTag("Skin"));
		   $death = new DeathHuman($player->getLevel(), $deathnbt);
		   $death->getDataPropertyManager()->setBlockPos(DeathHuman::DATA_PLAYER_BED_POSITION, new Vector3($player->getX(), $player->getY(), $player->getZ()));
		   $death->setPlayerFlag(DeathHuman::DATA_PLAYER_FLAG_SLEEP, true);
		   $death->setNameTag("§7[§cDeath§7]§r\n§f" .$player->getName(). "");
		   $death->setNameTagAlwaysVisible(false);
		   $death->spawnToAll();
		   $this->getScheduler()->scheduleDelayedTask(new DeathHumanDespawn($this, $death, $player), 1700);
           $player->sendMessage("§l§cMOVE LIKE PAIN, BE STEADY LIKE A DEATH");
	  }
	
		public function onChat(PlayerChatEvent $ev): void{
			$player = $ev->getPlayer();
		    if(isset($this->ichat[$player->getName()])){
                $ev->setCancelled(true);
                $player->sendMessage("§l§8(§6!§8)§r §6Please wait before you chat again in few seconds!");
        }  
        if(!$player->hasPermission("implactor.chatcooldown")){
            $this->chat[$player->getName()] = true;
            $this->getScheduler()->scheduleDelayedTask(new ChatCooldownTask($this, $player), 205);
            }
	  }
	
		public function onDamage(EntityDamageEvent $ev): void{
			$entity = $ev->getEntity();
			$cause = $ev->getCause();
			if($entity instanceof Player){
		    if($cause === EntityDamageEvent::CAUSE_FALL){
			}
			if($cause !== $ev::CAUSE_FALL){
				if(!$entity instanceof Player) return;
				if($entity->isCreative()) return;
				if($entity->getAllowFlight() == true){
					$entity->setFlying(false);
					$entity->setAllowFlight(false);
					$entity->sendMessage("§l§7(§c!§7)§r §cYour fly ability have been disabled because you suffered damaged§e...");
					}
				}
				if(isset($this->wild[$entity->getName()])){
                    unset($this->wild[$entity->getName()]);
                    $ev->setCancelled(true);
                }
				$entity->getLevel()->addParticle(new Bloodful($entity, Block::get(152)));      
			}
			if($entity instanceof DeathHuman) $ev->setCancelled(true);
	  }
	
		public function summonBot(Player $player, string $botname): void{
			$botnbt = Entity::createBaseNBT($player, null, 2, 2);
		    $botnbt->setTag($player->namedtag->getTag("Skin"));
		    $bot = new BotHuman($player->getLevel(), $botnbt);
		    $bot->setNameTag("§7[§bBot§7]§r\n§f" .$botname. "");
		    $bot->setNameTagAlwaysVisible(true);
		    $bot->spawnToAll();
	  }
	
		public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
			if(strtolower($command->getName()) == "bot") {
			if($sender instanceof Player){
			if($sender->hasPermission("implactor.bot")){
			if(count($args) < 1){
				$sender->sendMessage("§l§8(§6!§8)§r §cCommand usage§8:§r§7 /bot <name>");
				return false;
				}
				$this->summonBot($sender, $args[0]);
				$sender->sendMessage("§eYou have summoned a §bbot §ewith named§c§r " . $args[0]);
				$sender->getServer()->broadcastMessage("§7[§bBot§7]§f §e". $sender->getPlayer()->getName() ."§f has summoned a §bbot §fwith named §d" .$args[0]. "§f!");
                $sender->getLevel()->addSound(new Bot($sender));
             }else{
                $sender->sendMessage("§cYou have no permission allowed to use special §bBot §ccommand§e!");
	            return false;
			   }
			}else{
			    $sender->sendMessage("§cPlease use Implactor command in-game server!");
			    return false;
			   }
			    return true;
		   }
		   if(strtolower($command->getName()) == "icast") {
		   if($sender instanceof Player){
		   if($sender->hasPermission("implactor.broadcast")){
		   if(count($args) < 1){
			   $sender->sendMessage("§8§l(§6!§8)§r §cCommand usage§8:§r§7 /icast <message>");
			   return false;
			  }   
                           $sender->getServer()->broadcastMessage("§7[§bImplacast§7] §e" . implode(" ", $args));
			}else{
				$sender->sendMessage("§cYou have no permission allowed to use §eImplacast §ccommand§e!");
				return false;
			   }
		   }else{
			  $sender->sendMessage("§cPlease use Implactor command in-game server!");
		      return false;
			 }
			 return true;
	     }
	     if(strtolower($command->getName()) == "ibook") {
		 if($sender instanceof Player){
		 if($sender->hasPermission("implactor.book")){
			 $this->getBook($sender);
			 $sender->sendMessage("§6You has given a §aBook §bof §cImplactor§6!\n§fRead inside the book, §b". $sender->getPlayer()->getName() ."§f!");
             $sender->getLevel()->addSound(new Book($sender));
		  }else{
			 $sender->sendMessage("§cYou have no permission allowed to use §dBook §ccommand§e!");
			 return false;
			}
	    }else{
		    $sender->sendMessage("§cPlease use Implactor command in-game server!");
		    return false;
		   }
		   return true;
		}
		if(strtolower($command->getName()) == "ping") {
		if($sender instanceof Player){
		if($sender->hasPermission("implactor.ping")){
			$sender->sendMessage($sender->getPlayer()->getName(). "§a's ping status: §7[§d". $sender->getPing() ."§ems§7]");
         }else{
			$sender->sendMessage("§cYou have no permission allowed to use §aPing §ccommand§e!");
			return false;
			}
		}else{
			$sender->sendMessage("§cPlease use Implactor command in-game server!");
			return false;
           }
           return true;
		}
		if(strtolower($command->getName()) == "wild") {
		if($sender instanceof Player){
		if($sender->hasPermission("implactor.wild")){
			$x = rand(1,999);
            $y = 128;
            $z = rand(1,999);
            $sender->teleport($sender->getLevel()->getSafeSpawn(new Vector3($x, $y, $z)));            
			$sender->addTitle("§l§k§a!§b¡§c!§r §7§l[§eTeleporting§7]§r §l§k§a!§b¡§c!§r", "...");
            $sender->sendMessage("--------\n §eTeleporting to random spot\n §eof §bwilderness! \n§r--------");
		    $sender->sendPopup("§bYou won't take any fall damage when you teleported to the air!");
			$this->wild[$sender->getName()] = true;
         }else{
			$sender->sendMessage("");
			return false;
			}
		}else{
			$sender->sendMessage("§cPlease use Implactor command in-game server!");
			return false;
			}
            return true;
		}
	    if(strtolower($command->getName()) == "ihelp") {
		if($sender instanceof Player){
		if($sender->hasPermission("implactor.command.help")){
        if(count($args) == 0){
			$sender->sendMessage("§8§l(§6!§8)§r §cCommand usage§8:§r§7 /ihelp §e[1-3]");
         }else{
			if(count($args) == 1){
            switch($args[0]){
			case "1":
			     $sender->sendMessage("§b--(§a Implactor Help §7[§e1-3§7] §b)--");
			     $sender->sendMessage("§e/ihelp §9- §fCheck all commands list available!");
			     $sender->sendMessage("§e/iabout §9- §fAbout Implactor!");
			     $sender->sendMessage("§e/ping §9- §fPing your connection in-game server!");
			     $sender->sendMessage("§e/bot §9- §fSpawn the §bbot human§f!");
			     $sender->sendMessage("§e/icast §9- §fBroadcast your message to all online players!");
			     break;
			case "2":
			     $sender->sendMessage("§b--(§a Implactor Help §7[§e2-3§7] §b)--");
			     $sender->sendMessage("§e/wild §9- §fTeleport to a random spot of wilderness!");
			     $sender->sendMessage("§e/pvisible §9- §fOpen the player visibility menu UI!");
			     $sender->sendMessage("§e/vision §9- §fOpen the vision menu UI!");
			     $sender->sendMessage("§e/ibook §9- §fGet a book of §6Implactor§f!");
			     $sender->sendMessage("§e/gms §9- §fChange the gamemode to §c§lSURVIVAL");
			     break;
			case "3":
			     $sender->sendMessage("§b--(§a Implactor Help §7[§e3-3§7] §b)--");
			     $sender->sendMessage("§e/gmc §9- §fChange the gamemode to §e§lCREATIVE");
			     $sender->sendMessage("§e/gma §9- §fChange the gamemode to §b§lADVENTURE");
			     $sender->sendMessage("§e/gmsc §9- §fChange the gamemode to §b§lSPECTATOR");
			     break;
			    }
              }
		    }
         }else{
			$sender->sendMessage("");
			return false;
			}
		}else{
			$sender->sendMessage("§cPlease use Implactor command in-game server!");
			return false;
			}
			return true;
	    }
	    if(strtolower($command->getName()) == "iabout") {
		if($sender instanceof Player){
		if($sender->hasPermission("implactor.command.about")){
			$sender->sendMessage("§8---=========================---");
			$sender->sendMessage("§8- §aImpl§6actor");
			$sender->sendMessage("§8- §cAuthor: §fZadezter");
			$sender->sendMessage("§8- §aTeam: §fImpladeDeveloped");
			$sender->sendMessage("§8- §bCreated: §f23 §eMay §f2018");
		    $sender->sendMessage("§8- §6API: §f3.x.x - 4.0.0");
		    $sender->sendMessage("§8- §dRemaked: §f14 §eJuly §f2018");
			$sender->sendMessage("§8---=========================---");
		}else{
			$sender->sendMessage("");
			return false;
			}
		}else{
			$sender->sendMessage("§cPlease use Implactor command in-game server!");
			return false;
			}
			return true;
		}
		if(strtolower($command->getName()) == "gms") {
		if(!$sender instanceof Player){
		}
		if(!$sender->hasPermission("implactor.gamemode")){
		}
		if(empty($args[0])){
        $sender->setGamemode(Player::SURVIVAL); 
	    $sender->sendMessage("§aYou have changed the gamemode to §c§lSURVIVAL");
        return false;
		}
        $player = $this->getServer()->getPlayer($args[0]);
         if($this->getServer()->getPlayer($args[0])){
             $player->setGamemode(Player::SURVIVAL);
             $sender->sendMessage("§aYou have successfully changed §f". $player->getName() . "§a's gamemode to §c§lSURVIVAL");
             $player->sendMessage($sender->getName() . " §achanged your gamemode to §c§lSURVIVAL");
          }else{
             $sender->sendMessage("§cPlayer not found in-game server!");
              return false;
			}
			return true;
		}
		if(strtolower($command->getName()) == "gmc") {
		if(!$sender instanceof Player){
		}
		if(!$sender->hasPermission("implactor.gamemode")){
		}
		if(empty($args[0])){
        $sender->setGamemode(Player::CREATIVE); 
	    $sender->sendMessage("§aYou have changed the gamemode to §e§lCREATIVE");
        return false;
		}
        $player = $this->getServer()->getPlayer($args[0]);
         if($this->getServer()->getPlayer($args[0])){
             $player->setGamemode(Player::CREATIVE);
             $sender->sendMessage("§aYou have successfully changed §f". $player->getName() . "§a's gamemode to §e§lCREATIVE");
             $player->sendMessage($sender->getName() . " §achanged your gamemode to §e§lCREATIVE");
          }else{
             $sender->sendMessage("§cPlayer not found in-game server!");
              return false;
			}
			return true;
		}
		if(strtolower($command->getName()) == "gma") {
		if(!$sender instanceof Player){
		}
		if(!$sender->hasPermission("implactor.gamemode")){
		}
		if(empty($args[0])){
        $sender->setGamemode(Player::ADVENTURE); 
	    $sender->sendMessage("§aYou have changed the gamemode to §b§lADVENTURE");
        return false;
		}
        $player = $this->getServer()->getPlayer($args[0]);
         if($this->getServer()->getPlayer($args[0])){
             $player->setGamemode(Player::ADVENTURE);
             $sender->sendMessage("§aYou have successfully changed §f". $player->getName() . "§a's gamemode to §b§lADVENTURE");
             $player->sendMessage($sender->getName() . " §achanged your gamemode to §b§lADVENTURE");
          }else{
             $sender->sendMessage("§cPlayer not found in-game server!");
              return false;
			}
			return true;
		}
		if(strtolower($command->getName()) == "gmsc") {
		if(!$sender instanceof Player){
		}
		if(!$sender->hasPermission("implactor.gamemode")){
		}
		if(empty($args[0])){
        $sender->setGamemode(Player::SPECTATOR); 
	    $sender->sendMessage("§aYou have changed the gamemode to §7§lSPECTATOR");
        return false;
		}
        $player = $this->getServer()->getPlayer($args[0]);
         if($this->getServer()->getPlayer($args[0])){
             $player->setGamemode(Player::SPECTATOR);
             $sender->sendMessage("§aYou have successfully changed §f". $player->getName() . "§a's gamemode to §7§lSPECTATOR");
             $player->sendMessage($sender->getName() . " §achanged your gamemode to §7§lSPECTATOR");
          }else{
             $sender->sendMessage("§cPlayer not found in-game server!");
              return false;
			}
			return true;
		}
		if(strtolower($command->getName()) == "vision") {
		if($sender instanceof Player){
		if($sender->hasPermission("implactor.vision")){
		    $this->visionMenuUI($sender);
		}else{
            $sender->sendMessage("§cYou have no permission allowed to use §ePlayer visibility §ccommand§e!");
            return false;
            }            
        }else{
            $sender->sendMessage("§cPlease use Implactor command in-game server!");
            return false;
           }
           return true;
		}
		if(strtolower($command->getName()) == "pvisible") {
		if($sender instanceof Player){
		if($sender->hasPermission("implactor.playervisibility")){
		    $this->visiblePlayerMenuUI($sender);
		}else{
            $sender->sendMessage("§cYou have no permission allowed to use §ePlayer visibility §ccommand§e!");
            return false;
            }            
        }else{
            $sender->sendMessage("§cPlease use Implactor command in-game server!");
            return false;
           }
           return true;
           }
	  }
	    
	    public function visiblePlayerMenuUI($sender): void{
		    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
            $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if($result == null){
            }
            switch ($result){
            case 0:
            $sender->addTitle("§7§l[§aON§7]", "§aEnabled the player visibility!");
            unset($this->visibility[array_search($sender->getName(), $this->visibility)]);
			foreach ($this->getServer()->getOnlinePlayers() as $visibler) {
		    $sender->showplayer($visibler);
		    }
            break;
            case 1:
            $sender->addTitle("§7§l[§cOFF§7]", "§eDisabled the player visibility!");
            $this->visibility[] = $sender->getName();
			foreach ($this->getServer()->getOnlinePlayers() as $visibler) {
	        $sender->hideplayer($visibler);
	        }
            break;
            case 2:
            $sender->sendMessage("§cYou have closed the player visibility menu UI mode...");
            break;
            }
         });
         $form->setTitle("Implactor Menu UI");
         $form->setContent("§f> §0Player Visibility\n§eShow or hide their visibility!");
         $form->addButton("§aSHOW", 1, "https://cdn.discordapp.com/attachments/442624759985864714/468316318060249098/Show.png");
         $form->addButton("§4HIDE", 2, "https://cdn.discordapp.com/attachments/442624759985864714/468316318060249099/Hide.png");
         $form->addButton("§0CLOSE", 3, "https://cdn.discordapp.com/attachments/442624759985864714/468316717169508362/Logopit_1531725791540.png");
         $form->sendToPlayer($sender);
     }
     
        public function visionMenuUI($sender): void{
		    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
            $form = $api->createSimpleForm(function (Player $sender, $data){
            $result = $data;
            if($result == null){
            }
            switch ($result){
            case 0:
            $sender->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 1000000, 254, true));
            $sender->sendMessage("§eYou have §aenabled the §bNight Vision §emode!");
            break;
            case 1:
            $sender->removeEffect(Effect::NIGHT_VISION);
            $sender->sendMessage("§eYou have §cdisabled the §bNight Vision §emode!");
            break;
            case 2:
            $sender->sendMessage("§cYou have closed the vision menu UI mode...");
            break;
            }
         });
         $form->setTitle("Implactor Menu UI");
         $form->setContent("§f§l> §r§0Vision Mode\n§eGet some light while on night mode!");
         $form->addButton("§aENABLE", 1, "https://cdn.discordapp.com/attachments/442624759985864714/468316317351542804/On.png");
         $form->addButton("§4DISABLE", 2, "https://cdn.discordapp.com/attachments/442624759985864714/468316317351542806/Off.png");
         $form->addButton("§0CLOSE", 3, "https://cdn.discordapp.com/attachments/442624759985864714/468316717169508362/Logopit_1531725791540.png");
         $form->sendToPlayer($sender);
     }
     
		public function getBook(Player $player): void{
			$ibook = Item::get(Item::WRITTEN_BOOK, 0, 1);
		    $ibook->setTitle("§l§aBook §bof §cImplactor");
		    $ibook->setPageText(0, "§4We will add more informations soon.");
		    $ibook->setAuthor("§l§eZadezter");
		    $player->getInventory()->addItem($ibook);
	  }
	
		public function clearDroppedItems(): int{
			$i = 0;
            foreach($this->getServer()->getLevels() as $level){
            foreach($level->getEntities() as $entity){
             if(!$this->isEntityExempted($entity) && !($entity instanceof Creature)){
                 $entity->close();
                 $i++;
                 }
              }
           }
           return $i;
	  }
	
        public function clearSpawnedMobs(): int{
        	$i = 0;
            foreach($this->getServer()->getLevels() as $level){
            foreach($level->getEntities() as $entity){
             if(!$this->isEntityExempted($entity) && $entity instanceof Creature && !($entity instanceof Human)){
                 $entity->close();
                 $i++;
                 }
              }
           }
           return $i;
      }
      
        public function exemptEntity(Entity $entity): void{
        	$this->exemptedEntities[$entity->getID()] = $entity;
      }
      
        public function isEntityExempted(Entity $entity): bool{
        	return isset($this->exemptedEntities[$entity->getID()]);
      }
 }
              
	
	
