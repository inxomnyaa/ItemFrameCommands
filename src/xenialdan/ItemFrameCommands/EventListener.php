<?php

namespace xenialdan\ItemFrameCommands;

use pocketmine\block\Block;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\tile\ItemFrame;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{
	/** @var Loader */
	public $owner;

	public function __construct(Plugin $plugin){
		$this->owner = $plugin;
	}

	public function interactFrames(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if ($block->getId() == Block::ITEM_FRAME_BLOCK){
			/** @var ItemFrame $itemframe */
			if (($itemframe = $player->getLevel()->getTile($block)) instanceof ItemFrame){
				if ($player->hasPermission('frame.edit') && array_key_exists($player->getLowerCaseName(), Loader::$editing) && Loader::$editing[$player->getLowerCaseName()] !== Loader::EDIT_NONE){
					switch (Loader::$editing[$player->getLowerCaseName()]){
						case Loader::EDIT_ADDCOMMAND: {
							$this->addCommand($player, $itemframe);
							$event->setCancelled();
							break;
						}
						case Loader::EDIT_REMOVECOMMAND: {
							$this->removeCommand($player, $itemframe);
							$event->setCancelled();
							break;
						}
						case Loader::EDIT_REMOVEALLCOMMANDS: {
							$this->removeAllCommands($player, $itemframe);
							$event->setCancelled();
							break;
						}
						case Loader::EDIT_LISTCOMMANDS: {
							$this->listCommands($player, $itemframe);
							$event->setCancelled();
							break;
						}
						case Loader::EDIT_SETNAME: {
							$this->setName($player, $itemframe);
							$event->setCancelled();
							break;
						}
						case Loader::EDIT_REMOVENAME: {
							$this->removeName($player, $itemframe);
							$event->setCancelled();
							break;
						}
						case Loader::EDIT_SETITEM: {
							$this->setItem($player, $itemframe);
							$event->setCancelled();
							break;
						}
						case Loader::EDIT_REMOVEITEM: {
							$this->removeItem($player, $itemframe);
							$event->setCancelled();
							break;
						}
						default: {
						}
					}

					Loader::$editing[$player->getLowerCaseName()] = Loader::EDIT_NONE;
					unset(Loader::$editvalues[$player->getLowerCaseName()]);
					return;
				} elseif (isset($itemframe->namedtag->commands)){
					$event->setCancelled();
                    $nbt = $itemframe->getSpawnCompound();
                    $commands = $nbt->getListTag("commands");
                    foreach ($commands->getAllValues() as $id => $command) {
						if ($command['as'] === 'console')
							$player->getServer()->dispatchCommand(new ConsoleCommandSender(), str_ireplace("{PLAYER}", $player->getName(), $command['cmd']));
						if ($command['as'] === 'player')
							$player->getServer()->dispatchCommand($player, str_ireplace("{PLAYER}", $player->getName(), $command['cmd']));
					}
				}
			}
		}
	}

	private function addCommand(Player $player, ItemFrame $itemframe){
        $nbt = $itemframe->getSpawnCompound();
        $commands = $nbt->getListTag("commands")->getAllValues();
		$commands[] = [new StringTag('as', array_shift(Loader::$editvalues[$player->getLowerCaseName()])), new StringTag('cmd', $command = implode(" ", Loader::$editvalues[$player->getLowerCaseName()]))];
        $itemframe->getSpawnCompound()->setTag(new ListTag("commands", $commands));
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.addcmd.adding", [$command]));
	}

	private function removeCommand(Player $player, ItemFrame $itemframe){
		$index = intval(array_shift(Loader::$editvalues[$player->getLowerCaseName()]));
		$command = $this->owner->getLanguage()->translateString("command.delcmd.none");
        $nbt = $itemframe->getSpawnCompound();
        $commands = $nbt->getListTag("commands")->getAllValues();
		if (isset($commands[$index])){
			$command = $commands[$index]['cmd'];
			unset($commands[$index]);
			$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.delcmd.removing", [$command]));
		} else{
			$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.delcmd.removing.failed", [$command]));
		}
        $itemframe->getSpawnCompound()->setTag(new ListTag("commands", $commands));
	}

	private function removeAllCommands(Player $player, ItemFrame $itemframe){
		$command = $this->owner->getLanguage()->translateString("command.delcmd.none");
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.delallcmd.removing", [$command]));
        $itemframe->getSpawnCompound()->setTag(new ListTag("commands", []));
	}

	private function listCommands(Player $player, ItemFrame $itemframe){
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("divider"));
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.list.header"));
        $nbt = $itemframe->getSpawnCompound();
        $listTag = $nbt->getListTag("commands");
        if ($listTag instanceof ListTag) {
            $commands = $listTag->getAllValues();
			foreach ($commands as $id => $command){
				$player->sendMessage(TextFormat::GOLD . "[" . $id . "]" . TextFormat::GREEN . "[" . $command['as'] . "] " . TextFormat::GREEN . ">> " . $command['cmd']);
			}
		} else{
			$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.nocommands"));
		}
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("divider"));
	}

	private function setName(Player $player, ItemFrame $itemframe){
		if (!$itemframe->hasItem()){
			$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.setname.noitem"));
			return;
		}
		$itemframe->getItem()->setCustomName(($name = implode(" ", Loader::$editvalues[$player->getLowerCaseName()])));
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.setname.set", [$name]));
	}

	private function removeName(Player $player, ItemFrame $itemframe){
		if (!$itemframe->hasItem()){
			$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.setname.noitem"));
			return;
		}
		$itemframe->getItem()->clearCustomName();
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.removename.removing"));
	}

	private function setItem(Player $player, ItemFrame $itemframe){
		if (empty(Loader::$editvalues)){
			$itemframe->setItem(($item = clone $player->getInventory()->getItemInHand()));
		} else{
			$itemframe->setItem(($item = Item::get(...Loader::$editvalues[$player->getLowerCaseName()])));
		}
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.setitem.set", $item->__toString()));
	}

	private function removeItem(Player $player, ItemFrame $itemframe){
		$itemframe->setItem();
		$player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.removeitem.removing"));
	}
}