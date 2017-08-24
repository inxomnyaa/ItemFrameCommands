<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use xenialdan\ItemFrameCommands\Loader;

class RemoveAllSubCommand extends SubCommand{
	/** @var Loader */
	private $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		parent::__construct($plugin);
	}

	public function execute(CommandSender $sender, array $args): bool{
		$player = $sender->getServer()->getPlayer($sender->getName());
		Loader::$editing[$player->getLowerCaseName()] = Loader::EDIT_REMOVEALLCOMMANDS;
		return true;
	}

	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public function canUse(CommandSender $sender){
		return ($sender instanceof Player) and $sender->hasPermission("frame.delallcmd");
	}

	/**
	 * @return string
	 */
	public function getUsage(){
		return $this->plugin->getLanguage()->translateString("command.delallcmd.usage", []);
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->plugin->getLanguage()->translateString("command.delallcmd", []);
	}

	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->plugin->getLanguage()->translateString("command.delallcmd.desc", []);
	}

	/**
	 * @return string[]
	 */
	public function getAliases(){
		return [];
	}
}
