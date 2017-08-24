<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use xenialdan\ItemFrameCommands\Loader;

class AddSubCommand extends SubCommand{
	/** @var Loader */
	private $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		parent::__construct($plugin);
	}

	public function execute(CommandSender $sender, array $args): bool{
		$player = $sender->getServer()->getPlayer($sender->getName());
		if (count($args) < 2) return false;
		if ($args[0] !== 'player' && $args[0] !== 'console') return false;
		str_replace('/', '', $args[1]);
		Loader::$editing[$player->getLowerCaseName()] = Loader::EDIT_ADDCOMMAND;
		Loader::$editvalues[$player->getLowerCaseName()] = $args;
		return true;
	}

	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public function canUse(CommandSender $sender){
		return ($sender instanceof Player) and $sender->hasPermission("frame.addcmd");
	}

	/**
	 * @return string
	 */
	public function getUsage(){
		return $this->plugin->getLanguage()->translateString("command.addcmd.usage", []);
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->plugin->getLanguage()->translateString("command.addcmd", []);
	}

	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->plugin->getLanguage()->translateString("command.addcmd.desc", []);
	}

	/**
	 * @return string[]
	 */
	public function getAliases(){
		return [];
	}
}
