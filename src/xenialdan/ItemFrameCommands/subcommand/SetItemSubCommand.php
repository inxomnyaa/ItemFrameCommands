<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\ItemFrameCommands\Loader;

class SetItemSubCommand extends BaseSubCommand
{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->setPermission("frame.setitem");
        $itemArgument = new class("itemName", true) extends RawStringArgument
        {

            public function getTypeName(): string
            {
                return "Item";
            }

            public function canParse(string $testString, CommandSender $sender): bool
            {
                try {
                    $item = ItemFactory::fromString($testString, false);
                } catch (\InvalidArgumentException $e) {
                    return false;
                }
                return $item instanceof Item;
            }

            public function parse(string $argument, CommandSender $sender)
            {
                try {
                    $item = ItemFactory::fromString($argument, false);
                } catch (\InvalidArgumentException $e) {
                    Loader::getInstance()->getLogger()->logException($e);
                    return null;
                }
                return $item;
            }
        };
        $this->registerArgument(0, $itemArgument);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(Loader::getInstance()->getLanguage()->translateString("runingame"));
            return;
        }
        Loader::$editing[$sender->getLowerCaseName()] = Loader::EDIT_SETITEM;
        Loader::$editvalues[$sender->getLowerCaseName()] = $args["itemName"] ?? $sender->getInventory()->getItemInHand();
        $sender->sendMessage(TextFormat::GREEN . Loader::getInstance()->getLanguage()->translateString("command.click"));
    }
}
