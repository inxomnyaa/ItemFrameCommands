<?php

namespace xenialdan\ItemFrameCommands;

use pocketmine\block\Block;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\tile\ItemFrame;
use pocketmine\utils\TextFormat;

class EventListener implements Listener
{
    /** @var Loader */
    public $owner;

    public function __construct(Plugin $plugin)
    {
        $this->owner = $plugin;
    }

    /**
     * @param PlayerInteractEvent $event
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     * @throws \InvalidStateException
     * @throws \RuntimeException
     */
    public function interactFrames(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if ($block->getId() == Block::ITEM_FRAME_BLOCK) {
            /** @var ItemFrame $itemframe */
            if (($itemframe = $player->getLevel()->getTile($block)) instanceof ItemFrame) {
                if ($player->hasPermission('frame.edit') && array_key_exists($player->getLowerCaseName(), Loader::$editing) && Loader::$editing[$player->getLowerCaseName()] !== Loader::EDIT_NONE) {
                    switch (Loader::$editing[$player->getLowerCaseName()]) {
                        case Loader::EDIT_ADDCOMMAND:
                            {
                                $this->addCommand($player, $itemframe);
                                $event->setCancelled();
                                break;
                            }
                        case Loader::EDIT_REMOVECOMMAND:
                            {
                                $this->removeCommand($player, $itemframe);
                                $event->setCancelled();
                                break;
                            }
                        case Loader::EDIT_REMOVEALLCOMMANDS:
                            {
                                $this->removeAllCommands($player, $itemframe);
                                $event->setCancelled();
                                break;
                            }
                        case Loader::EDIT_LISTCOMMANDS:
                            {
                                $this->listCommands($player, $itemframe);
                                $event->setCancelled();
                                break;
                            }
                        case Loader::EDIT_SETNAME:
                            {
                                $this->setName($player, $itemframe);
                                $event->setCancelled();
                                break;
                            }
                        case Loader::EDIT_REMOVENAME:
                            {
                                $this->removeName($player, $itemframe);
                                $event->setCancelled();
                                break;
                            }
                        case Loader::EDIT_SETITEM:
                            {
                                $this->setItem($player, $itemframe);
                                $event->setCancelled();
                                break;
                            }
                        case Loader::EDIT_REMOVEITEM:
                            {
                                $this->removeItem($player, $itemframe);
                                $event->setCancelled();
                                break;
                            }
                    }

                    Loader::$editing[$player->getLowerCaseName()] = Loader::EDIT_NONE;
                    unset(Loader::$editvalues[$player->getLowerCaseName()]);
                    return;
                } else if ($itemframe->hasItem() && ($nbt = $itemframe->getItem()->getNamedTag())->hasTag("commands", ListTag::class)) {
                    $event->setCancelled();
                    $commands = $nbt->getListTag("commands")->getValue();
                    /** @var StringTag $stringTag */
                    foreach ($commands as $stringTag) {
                        $val = $stringTag->getValue();
                        if ($this->isConsole($val))
                            $player->getServer()->dispatchCommand(new ConsoleCommandSender(), str_ireplace("{PLAYER}", $player->getName(), $this->getCommand($val)));
                        if (!$this->isConsole($val))
                            $player->getServer()->dispatchCommand($player, str_ireplace("{PLAYER}", $player->getName(), $this->getCommand($val)));
                    }
                }
            }
        }
    }

    private function getCommand(string $val): string
    {
        return substr($val, 2);
    }

    private function isConsole(string $val): bool
    {
        return $val[0] === 'c';
    }

    /**
     * @param Player $player
     * @param ItemFrame $itemframe
     * @throws \InvalidArgumentException
     */
    private function addCommand(Player $player, ItemFrame $itemframe)
    {
        if (!$itemframe->hasItem()) {
            $player->sendMessage(TextFormat::RED . $this->owner->getLanguage()->translateString("command.addcmd.noitem"));
            return;
        }
        $item = $itemframe->getItem();
        $nbt = $item->getNamedTag();
        if ($nbt->hasTag("commands", ListTag::class))
            $commands = $nbt->getListTag("commands");
        else
            $commands = new ListTag("commands", [], NBT::TAG_String);
        $pc = array_shift(Loader::$editvalues[$player->getLowerCaseName()]);
        $commands->push(new StringTag("", $pc . ":" . ($command = implode(" ", Loader::$editvalues[$player->getLowerCaseName()]))));
        $item->setNamedTagEntry($commands);
        $itemframe->setItem($item);
        $player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.addcmd.adding", [$command]));
    }

    /**
     * @param Player $player
     * @param ItemFrame $itemframe
     * @throws \RuntimeException
     */
    private function removeCommand(Player $player, ItemFrame $itemframe)
    {
        if (!$itemframe->hasItem()) {
            $player->sendMessage(TextFormat::RED . $this->owner->getLanguage()->translateString("command.addcmd.noitem"));
            return;
        }
        $index = intval(array_shift(Loader::$editvalues[$player->getLowerCaseName()]));
        $item = $itemframe->getItem();
        $nbt = $item->getNamedTag();
        if ($nbt->hasTag("commands", ListTag::class)) {
            $commands = $nbt->getListTag("commands");
            try {
                $command = $this->getCommand($commands->get($index)->getValue());
                $commands->remove($index);
                $player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.delcmd.removing", [$command]));
            } catch (\Exception $exception) {
                $player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.delcmd.failed", [$command ?? ""]));
                return;
            }
            $nbt->setTag($commands);
            $item->setNamedTag($nbt);
            $itemframe->setItem($item);
        } else $player->sendMessage(TextFormat::RED . $this->owner->getLanguage()->translateString("command.nocommands"));
    }

    /**
     * @param Player $player
     * @param ItemFrame $itemframe
     * @throws \RuntimeException
     */
    private function removeAllCommands(Player $player, ItemFrame $itemframe)
    {
        if (!$itemframe->hasItem()) {
            $player->sendMessage(TextFormat::RED . $this->owner->getLanguage()->translateString("command.addcmd.noitem"));
            return;
        }
        $command = $this->owner->getLanguage()->translateString("command.delcmd.none");
        $player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.delallcmd.removing", [$command]));
        $item = $itemframe->getItem();
        $item->getNamedTag()->setTag(new ListTag("commands", [], NBT::TAG_String));
        $itemframe->setItem($item);
    }

    /**
     * @param Player $player
     * @param ItemFrame $itemframe
     */
    private function listCommands(Player $player, ItemFrame $itemframe)
    {
        if (!$itemframe->hasItem()) {
            $player->sendMessage(TextFormat::RED . $this->owner->getLanguage()->translateString("command.addcmd.noitem"));
            return;
        }
        $player->sendMessage(TextFormat::GOLD . $this->owner->getLanguage()->translateString("divider"));
        $player->sendMessage(TextFormat::GOLD . $this->owner->getLanguage()->translateString("command.list.header"));
        $nbt = $itemframe->getItem()->getNamedTag();
        if ($nbt->hasTag("commands", ListTag::class) && !empty(($commands = $nbt->getListTag("commands")->getValue()))) {
            /** @var StringTag $stringTag */
            foreach ($commands as $id => $stringTag) {
                $command = $this->getCommand($stringTag->getValue());
                $pc = $this->isConsole($stringTag->getValue()) ? "Console" : "Player";
                $player->sendMessage(TextFormat::GOLD . "[" . $id . "]" . ($this->isConsole($stringTag->getValue()) ? TextFormat::GREEN : TextFormat::WHITE) . "[" . $pc . "] >> " . $command);
            }
        } else {
            $player->sendMessage(TextFormat::RED . $this->owner->getLanguage()->translateString("command.nocommands"));
        }
        $player->sendMessage(TextFormat::GOLD . $this->owner->getLanguage()->translateString("divider"));
    }

    /**
     * @param Player $player
     * @param ItemFrame $itemframe
     */
    private function setName(Player $player, ItemFrame $itemframe)
    {
        if (!$itemframe->hasItem()) {
            $player->sendMessage(TextFormat::RED . $this->owner->getLanguage()->translateString("command.setname.noitem"));
            return;
        }
        $itemframe->setItem($itemframe->getItem()->setCustomName(($name = Loader::$editvalues[$player->getLowerCaseName()])));
        $player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.setname.set", [$name]));
    }

    /**
     * @param Player $player
     * @param ItemFrame $itemframe
     */
    private function removeName(Player $player, ItemFrame $itemframe)
    {
        if (!$itemframe->hasItem()) {
            $player->sendMessage(TextFormat::RED . $this->owner->getLanguage()->translateString("command.setname.noitem"));
            return;
        }
        $itemframe->getItem()->clearCustomName();
        $player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.removename.removing"));
    }

    /**
     * @param Player $player
     * @param ItemFrame $itemframe
     */
    private function setItem(Player $player, ItemFrame $itemframe)
    {
        if (empty(Loader::$editvalues)) {
            $itemframe->setItem(($item = clone $player->getInventory()->getItemInHand()));
        } else {
            $itemframe->setItem(($item = Loader::$editvalues[$player->getLowerCaseName()]));
        }
        $player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.setitem.set", [$item->__toString()]));
    }

    /**
     * @param Player $player
     * @param ItemFrame $itemframe
     */
    private function removeItem(Player $player, ItemFrame $itemframe)
    {
        $itemframe->setItem();
        $player->sendMessage(TextFormat::GREEN . $this->owner->getLanguage()->translateString("command.removeitem.removing"));
    }
}