<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\ItemFrameCommands\Loader;

class AbortSubCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("frame.abort");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(Loader::getInstance()->getLanguage()->translateString("runingame"));
            return;
        }
        Loader::$editing[$sender->getLowerCaseName()] = Loader::EDIT_NONE;
        unset(Loader::$editvalues[$sender->getLowerCaseName()]);
        $sender->sendMessage(TextFormat::GREEN . Loader::getInstance()->getLanguage()->translateString("command.abort.aborting"));
    }
}
