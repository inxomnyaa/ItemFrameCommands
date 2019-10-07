<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\ItemFrameCommands\Loader;

class SetNameSubCommand extends BaseSubCommand
{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->setPermission("frame.setname");
        $this->registerArgument(0, new TextArgument("name"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(Loader::getInstance()->getLanguage()->translateString("runingame"));
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage($this->getUsageMessage());
            return;
        }
        Loader::$editing[$sender->getLowerCaseName()] = Loader::EDIT_SETNAME;
        Loader::$editvalues[$sender->getLowerCaseName()] = (string)$args["name"];
        $sender->sendMessage(TextFormat::GREEN . Loader::getInstance()->getLanguage()->translateString("command.click"));
    }
}
