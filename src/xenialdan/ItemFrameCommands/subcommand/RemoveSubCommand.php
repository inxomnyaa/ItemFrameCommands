<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use xenialdan\ItemFrameCommands\Loader;

class RemoveSubCommand extends SubCommand{
	/** @var Loader */
	private $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		parent::__construct($plugin);
	}

	public function execute(CommandSender $sender, array $args): bool{
		$player = $sender->getServer()->getPlayer($sender->getName());
		if (count($args) < 1) return false;
		if (!is_numeric($args[0])) return false;
		$args[0] = intval($args[0]);
		Loader::$editing[$player->getLowerCaseName()] = Loader::EDIT_REMOVECOMMAND;
		Loader::$editvalues[$player->getLowerCaseName()] = $args;
		return true;
	}

	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public function canUse(CommandSender $sender){
		return ($sender instanceof Player) and $sender->hasPermission("frame.delcmd");
	}

	/**
	 * @return string
	 */
	public function getUsage(){
		return $this->plugin->getLanguage()->translateString("command.delcmd.usage", []);
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->plugin->getLanguage()->translateString("command.delcmd", []);
	}

	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->plugin->getLanguage()->translateString("command.delcmd.desc", []);
	}

	/**
	 * @return string[]
	 */
	public function getAliases(){
		return [];
	}
}
