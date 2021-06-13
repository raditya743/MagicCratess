<?php


namespace Hebbinkpro\MagicCrates\commands\subcommands;


use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use Hebbinkpro\MagicCrates\Main;
use pocketmine\command\CommandSender;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class CrateKey extends BaseSubCommand
{
	private Main $main;

    protected function prepare(): void
    {
		$this->main = Main::getInstance();

		$this->setPermission("magiccrates.cmd.makekey");

    	$this->registerArgument(0, new RawStringArgument("type", true));
		$this->registerArgument(1, new IntegerArgument("amount", true));
		$this->registerArgument(2, new RawStringArgument("player", true));
	}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
		if(!$sender instanceof Player){
		    if(!isset($args["player"])){
                $sender->sendMessage("[§6Magic§cCrates§r] §cUsage: /mc makekey <type> <amount> <player>");
                return;
            }
		    if(is_null($this->main->getServer()->getPlayerByPrefix($args["player"]))){
                $sender->sendMessage("[§6Magic§cCrates§r] §cThe given player isn't online");
                return;
            }
		}

    	$types = [];

		foreach($this->main->getConfig()->get("types") as $key=>$content){
			$types[] = $key;
		}
        if(!isset($args["type"]) or !in_array($args["type"], $types)){
        	$msg = "";
        	foreach($types as $type){
        		$msg = $msg . $type . ", ";
			}
        	$sender->sendMessage("[§6Magic§cCrates§r] §cThat isn't a valid crate type");
        	$sender->sendMessage("§6Valid types:§r $msg");
        	return;
		}

        $key = ItemFactory::getInstance()->get(ItemIds::PAPER,0);
        $key->setCustomName("§e" . $args["type"] . " §r§dCrate Key");
        $key->setLore(["§6Magic§cCrates §7Key - " . $args["type"]]);
        $key->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));

        $count = 0;
        if(!isset($args["amount"])){
        	$count = 1;
		}else{
        	$count = $args["amount"];
		}
		$i = 0;

        if(isset($args["player"])){
        	if($this->main->getServer()->getPlayerByPrefix($args["player"]) instanceof Player){
        		$player = $this->main->getServer()->getPlayerByPrefix($args["player"]);
				while($i < $count){
					$i++;
					$player->getInventory()->addItem($key);
				}

				$name = $player->getName();
				$type = $args["type"];
				$player->sendMessage("[§6Magic§cCrates§r] §aYou received the §e$type §r§acrate key");
				$sender->sendMessage("[§6Magic§cCrates§r] §e$name §r§a received the §e$type §r§acrate key");
				return;
			}
		}
		while($i < $count){
			$i++;
			$sender->getInventory()->addItem($key);
		}
		$type = $args["type"];
        $sender->sendMessage("[§6Magic§cCrates§r] §aYou received the crate key for the §e$type §r§acrate");
    }
}