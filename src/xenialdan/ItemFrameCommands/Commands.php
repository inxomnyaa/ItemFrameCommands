<?php

namespace xenialdan\ItemFrameCommands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use xenialdan\ItemFrameCommands\subcommand\AbortSubCommand;
use xenialdan\ItemFrameCommands\subcommand\AddSubCommand;
use xenialdan\ItemFrameCommands\subcommand\ListSubCommand;
use xenialdan\ItemFrameCommands\subcommand\RemoveAllSubCommand;
use xenialdan\ItemFrameCommands\subcommand\RemoveItemSubCommand;
use xenialdan\ItemFrameCommands\subcommand\RemoveNameSubCommand;
use xenialdan\ItemFrameCommands\subcommand\RemoveSubCommand;
use xenialdan\ItemFrameCommands\subcommand\SetItemSubCommand;
use xenialdan\ItemFrameCommands\subcommand\SetNameSubCommand;
use xenialdan\ItemFrameCommands\subcommand\SubCommand;

class Commands extends PluginCommand{
	/** @var Loader */
	private $plugin;
	private $subCommands = [];

	/* @var SubCommand[] */
	private $commandObjects = [];

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		parent::__construct($this->plugin->getLanguage()->translateString("command.name", []), $plugin);
		$this->setAliases([]);
		$this->setPermission("frame");
		$this->setDescription($this->plugin->getLanguage()->translateString("command.desc", []));

		$this->loadSubCommand(new AbortSubCommand($plugin));
		$this->loadSubCommand(new AddSubCommand($plugin));
		$this->loadSubCommand(new ListSubCommand($plugin));
		$this->loadSubCommand(new RemoveAllSubCommand($plugin));
		$this->loadSubCommand(new RemoveItemSubCommand($plugin));
		$this->loadSubCommand(new RemoveNameSubCommand($plugin));
		$this->loadSubCommand(new RemoveSubCommand($plugin));
		$this->loadSubCommand(new SetItemSubCommand($plugin));
		$this->loadSubCommand(new SetNameSubCommand($plugin));
	}

	private function loadSubCommand(SubCommand $command){
		$this->commandObjects[] = $command;
		$commandId = count($this->commandObjects) - 1;
		$this->subCommands[$command->getName()] = $commandId;
		foreach ($command->getAliases() as $alias){
			$this->subCommands[$alias] = $commandId;
		}
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
		if (!isset($args[0])){
			return $this->sendHelp($sender);
		}
		$subCommand = strtolower(array_shift($args));
		if (!isset($this->subCommands[$subCommand])){
			return $this->sendHelp($sender);
		}
		$command = $this->commandObjects[$this->subCommands[$subCommand]];
		$canUse = $command->canUse($sender);
		if ($canUse){
			if (!$command->execute($sender, $args)){
				$sender->sendMessage($this->plugin->getLanguage()->translateString("command.usage", [$command->getName(), $command->getUsage()]));
			}
		} elseif (!($sender instanceof Player)){
			$sender->sendMessage(TextFormat::RED . $this->plugin->getLanguage()->translateString("runingame", []));
		} else{
			$sender->sendMessage(TextFormat::RED . $this->plugin->getLanguage()->translateString("noperm", []));
		}
		return true;
	}

	private function sendHelp(CommandSender $sender){
		$sender->sendMessage("===========[ItemFrameCommands commands]===========");
		foreach ($this->commandObjects as $command){
			if ($command->canUse($sender)){
				$sender->sendMessage($this->plugin->getLanguage()->translateString("subcommand.usage", [$command->getName(), $command->getUsage(), $command->getDescription()]));
			}
		}
		return true;
	}
}
