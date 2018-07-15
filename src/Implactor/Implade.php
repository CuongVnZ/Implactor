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

//* Implactor *//
use Implactor\tasks\SpawnTask;
use Implactor\tasks\AntiAdvertising;
use Implactor\tasks\AntiSwearing;
use Implactor\tasks\AntiCaps;
use Implactor\tasks\ChatCooldownTask;
use Implactor\tasks\ClearLaggTask;
use Implactor\particles\SpawnParticles;
use Implactor\particles\DeathParticles;
use Implactor\npc\DeathHuman;
use Implactor\npc\DeathHumanDespawn;
use Implactor\npc\bot\BotTask;
use Implactor\npc\bot\BotHuman;
use Implactor\npc\bot\BotListener;

//* Pocketmine *//
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerJoinEvent; 
use pocketmine\event\player\PlayerQuitEvent; 
use pocketmine\event\player\PlayerDeathEvent; 
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound as Join;
use pocketmine\level\sound\BlazeShootSound as Quit;
use pocketmine\level\sound\GhastSound as DeathOne;
use pocketmine\level\sound\AnvilCrashSound as DeathTwo;
use pocketmine\level\sound\DoorBumpSound as Bot;
use pocketmine\level\sound\FizzSound as Book;
use pocketmine\level\particle\DestroyBlockParticle as Bloodful;
use pocketmine\entity\Entity;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Creature;
use pocketmine\entity\Human;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\block\Block;
use pocketmine\nbt\NBT;
use pocketmine\item\Item;
use pocketmine\math\Vector3;

//* Others *//
use jojoe7777\FormAPI;

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
            $this->getScheduler()->scheduleRepeatingTask(new SpawnParticles($this, $this), 13);
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
          $this->formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
           if(is_null($this->formapi)){
             $this->getLogger()->warning("FormAPI not found in plugin folder! Disabled 2 UI commands...");
             $this->getPluginLoader()->disablePlugin($this);
            }
      }
    
       public function onDisable(): void{
       	$this->getLogger()->notice("Oh no, Implactor has self-destructed it's system!");
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
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 1000000, 1, false));
			$player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 1000000, 1, false));
			$player->sendMessage("§7[§aI§6R§7]§r §bThis server is running the Implactor plugin!");
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
			$player->removeEffect(Effect::SPEED);
			$player->removeEffect(Effect::JUMP_BOOST);
		if($player->isOP()){
			$ev->setJoinMessage("§7(§6STAFF§7) §l§8[§a+§8]§r §a{$player->getName()}");
			$player->getLevel()->addSound(new Quit($player));
         }else{
			$ev->setJoinMessage("§l§8[§a+§8]§r §a{$player->getName()}");
			$player->getLevel()->addSound(new Quit($player));
			}
	  }
	
		public function onRespawn(PlayerRespawnEvent $ev): void{
			$player = $ev->getPlayer();
			$player->setHealth(20);
	        $player->addTitle("§l§cYOU ARE DEAD", "§fOuch, what just happend?");
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
		   $this->getScheduler()->scheduleDelayedTask(new DeathHumanDespawn($this, $death, $player), 1600);
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
					$entity->sendMessage("§l§7(§c!§7)§r §cYour abilities have been disabled because you suffered damaged§e...");
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
	
		public function onMotion(EntityMotionEvent $ev): void{
			$entity = $event->getEntity();
            if($entity instanceof Living && !$entity instanceof Player){
                $event->setCancelled(true);
                }
	  }
	
		public function summonBot(Player $player, string $name): void{
			$botnbt = Entity::createBaseNBT($player, null, 2, 2);
		    $botnbt->setTag($player->namedtag->getTag("Skin"));
		    $bot = new BotHuman($player->getLevel(), $botnbt);
		    $bot->setNameTag("§7[§bBot§7]§r\n§f" .$name. "");
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
				$sender->sendMessage("\n§b- §aRemember, this bot human's weakness is §l§6FIRE §r§aor §l§6LAVA§r!");
				$sender->getServer()->broadcastMessage("§7[§bBot§7]§f ". IR::GOLD . $sender->getPlayer()->getName() . IR::WHITE ." has summoned a §bbot §fwith named §d" .$args[0]. "§f!");
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
			   $sender->getServer()->broadcastMessage("§7[§bImplacast§7] §b" . IR::YELLOW . implode(" ", $args));
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
			 $sender->sendMessage("§6You has given a §aBook §bof §cImplactor§6!\n§fRead inside the book, ". IR::AQUA. $sender->getPlayer()->getName(). "§f!");
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
			$sender->sendMessage($sender->getPlayer()->getName(). "§a's ping status: §7[§d{$sender->getPing} §ems§7]");
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
			$sender->getLevel()->addSound(new BlazeShootSound($sender));
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
			$sender->sendMessage("§8§l(§6!§8)§r §cCommand usage§8:§r§7 /ihelp §e[1-2]");
         }else{
			if(count($args) == 1){
            switch($args[0]){
			case "1":
			     $sender->sendMessage("§b--(§a Implactor Help §7[§e1-2§7] §b)--");
			     $sender->sendMessage("§e/ihelp §9- §fCheck all commands list available!");
			     $sender->sendMessage("§e/iabout §9- §fAbout Implactor!");
			     $sender->sendMessage("§e/ping §9- §fPing your connection in-game server!");
			     $sender->sendMessage("§e/bot §9- §fSpawn the §bbot human§f!");
			     $sender->sendMessage("§e/icast §9- §fBroadcast your message to all online players!");
			     break;
			case "2":
			     $sender->sendMessage("§b--(§a Implactor Help §7[§e2-2§7] §b)--");
			     $sender->sendMessage("§e/wild §9- §fTeleport to a random spot of wilderness!");
			     $sender->sendMessage("§e/pvisible §9- §fShow or hide the player visibility!");
			     $sender->sendMessage("§e/vision §9- §fUse the vision mode with a command!");
			     $sender->sendMessage("§e/ibook §9- §fGet a book of §6Implactor§f!");
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
			$sender->sendMessage(" §l§aImpl§6actor");
			$sender->sendMessage("§8- §cAuthor: §fZadezter");
			$sender->sendMessage("§8- §aTeam: §fImpladeDeveloped");
			$sender->sendMessage("§8- §bCreated: §f23 §eMay §f2018");
		    $sender->sendMessage("§8- §6API: §f3.0.2");
		    $sender->sendMessage("§8- §dRemaked: §f14 §eJuly 2018");
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
		if(strtolower($command->getName()) == "vision") {
		if($sender instanceof Player){
		if($sender->hasPermission("implactor.vision")){
		    $this->visionModeUI($sender);
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
		    $this->visiblePlayerUI($sender);
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
	    
	    public function visiblePlayerUI($sender): void{
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
            $sender->sendMessage("§cClosed the player visible UI mode...");
            break;
            }
         });
         $form->setTitle("§l§aImpl§6actor");
         $form->setContent("§e> §0Player Visibility");
         $form->addButton("§a§lShow Players", 0);
         $form->addButton("§c§lHide Players", 1);
         $form->addButton("§0§lClose", 2);
         $form->sendToPlayer($sender);
     }
     
        public function visionModeUI($sender): void{
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
            $sender->sendMessage("§cClosed the vision UI mode...");
            break;
            }
         });
         $form->setTitle("§l§aImpl§6actor");
         $form->setContent("§e> §0Vision Mode");
         $form->addButton("§a§lEnable", 0);
         $form->addButton("§c§lDisable", 1);
         $form->addButton("§0§lClose", 2);
         $form->sendToPlayer($sender);
     }
     
		public function getBook(Player $player): void{
			$ibook = Item::get(Item::WRITTEN_BOOK, 0, 1);
		    $ibook->setTitle(IR::GREEN . IR::UNDERLINE . "Book §bof §cImplactor");
		    $ibook->setPageText(0, "§4We will add more informations soon.");
		    $ibook->setPageText(1, " ");
		    $ibook->setAuthor("§l§eZadezter");
		    $player->getInventory()->addItem($ibook);
	  }
	
		public function clearItems(): int{
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
	
        public function clearMobs(): int{
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
              
	
	
