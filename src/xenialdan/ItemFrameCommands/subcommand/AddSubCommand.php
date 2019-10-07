<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use CortexPE\Commando\args\StringEnumArgument;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\ItemFrameCommands\Loader;

class AddSubCommand extends BaseSubCommand
{
    /**
     * @throws \CortexPE\Commando\exception\ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("frame.addcmd");
        $stringEnumArgument = new class("as") extends StringEnumArgument
        {
            protected const VALUES = [
                "player" => "p",
                "console" => "c",
            ];

            public function getTypeName(): string
            {
                return "string";
            }

            public function parse(string $argument, CommandSender $sender)
            {
                return $this->getValue($argument);
            }
        };
        $commandArgument = new class("command") extends TextArgument
        {
            public function getNetworkType(): int
            {
                return AvailableCommandsPacket::ARG_TYPE_COMMAND;
            }

            public function getTypeName(): string
            {
                return "command";
            }

            public function canParse(string $testString, CommandSender $sender): bool
            {
                $commandName = "";
                $args = explode(" ", $testString);
                $command = Loader::getInstance()->getServer()->getCommandMap()->matchCommand($commandName, $args);
                return $command instanceof Command;
            }
        };
        $this->registerArgument(0, $stringEnumArgument);
        $this->registerArgument(1, $commandArgument);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(Loader::getInstance()->getLanguage()->translateString("runingame"));
            return;
        }
        Loader::$editing[$sender->getLowerCaseName()] = Loader::EDIT_ADDCOMMAND;
        Loader::$editvalues[$sender->getLowerCaseName()] = $args;
        $sender->sendMessage(TextFormat::GREEN . Loader::getInstance()->getLanguage()->translateString("command.click"));
    }
}
