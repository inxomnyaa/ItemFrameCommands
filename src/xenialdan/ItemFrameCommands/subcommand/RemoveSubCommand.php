<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\ItemFrameCommands\Loader;

class RemoveSubCommand extends BaseSubCommand
{

    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->setPermission("frame.delcmd");
        $this->registerArgument(0, new IntegerArgument("index"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(Loader::getInstance()->getLanguage()->translateString("runingame"));
            return;
        }
        $args["index"] = intval($args["index"]);
        Loader::$editing[$sender->getLowerCaseName()] = Loader::EDIT_REMOVECOMMAND;
        Loader::$editvalues[$sender->getLowerCaseName()] = $args;
        $sender->sendMessage(TextFormat::GREEN . Loader::getInstance()->getLanguage()->translateString("command.click"));
    }
}
