<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\ItemFrameCommands\Loader;

class RemoveNameSubCommand extends BaseSubCommand
{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->setPermission("frame.removename");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(Loader::getInstance()->getLanguage()->translateString("runingame"));
            return;
        }
        Loader::$editing[$sender->getLowerCaseName()] = Loader::EDIT_REMOVENAME;
        $sender->sendMessage(TextFormat::GREEN . Loader::getInstance()->getLanguage()->translateString("command.click"));
    }
}
