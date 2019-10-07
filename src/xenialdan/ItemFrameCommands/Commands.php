<?php

namespace xenialdan\ItemFrameCommands;

use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use xenialdan\ItemFrameCommands\subcommand\AbortSubCommand;
use xenialdan\ItemFrameCommands\subcommand\AddSubCommand;
use xenialdan\ItemFrameCommands\subcommand\ListSubCommand;
use xenialdan\ItemFrameCommands\subcommand\RemoveAllSubCommand;
use xenialdan\ItemFrameCommands\subcommand\RemoveItemSubCommand;
use xenialdan\ItemFrameCommands\subcommand\RemoveNameSubCommand;
use xenialdan\ItemFrameCommands\subcommand\RemoveSubCommand;
use xenialdan\ItemFrameCommands\subcommand\SetItemSubCommand;
use xenialdan\ItemFrameCommands\subcommand\SetNameSubCommand;

class Commands extends BaseCommand
{

    protected function prepare(): void
    {
        $this->setPermission("frame");
        $lang = Loader::getInstance()->getLanguage();
        $this->registerSubCommand(new AbortSubCommand($lang->translateString("command.abort"), $lang->translateString("command.abort.desc")));
        $this->registerSubCommand(new AddSubCommand($lang->translateString("command.addcmd"), $lang->translateString("command.addcmd.desc")));
        $this->registerSubCommand(new ListSubCommand($lang->translateString("command.list"), $lang->translateString("command.list.desc")));
        $this->registerSubCommand(new RemoveAllSubCommand($lang->translateString("command.delallcmd"), $lang->translateString("command.delallcmd.desc")));
        $this->registerSubCommand(new RemoveItemSubCommand($lang->translateString("command.removeitem"), $lang->translateString("command.removeitem.desc")));
        $this->registerSubCommand(new RemoveNameSubCommand($lang->translateString("command.removename"), $lang->translateString("command.removename.desc")));
        $this->registerSubCommand(new RemoveSubCommand($lang->translateString("command.delcmd"), $lang->translateString("command.delcmd.desc")));
        $this->registerSubCommand(new SetItemSubCommand($lang->translateString("command.setitem"), $lang->translateString("command.setitem.desc")));
        $this->registerSubCommand(new SetNameSubCommand($lang->translateString("command.setname"), $lang->translateString("command.setname.desc")));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param BaseArgument[] $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage($this->getUsage());
    }
}
