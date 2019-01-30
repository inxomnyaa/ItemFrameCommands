<?php

namespace xenialdan\ItemFrameCommands\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use xenialdan\customui\elements\Dropdown;
use xenialdan\customui\elements\Input;
use xenialdan\customui\windows\CustomForm;
use xenialdan\ItemFrameCommands\Loader;

class AddSubCommand extends SubCommand{
	/** @var Loader */
	private $plugin;

	public function __construct(Plugin $plugin){
		$this->plugin = $plugin;
		parent::__construct($plugin);
	}

	public function execute(CommandSender $sender, array $args): bool{
        if (!$sender instanceof Player) return false;
        $form = new CustomForm("Add command");
        $form->addElement(new Dropdown("Execute as", ["player", "console"]));
        $form->addElement(new Input("Command", "Command"));
        $form->setCallable(function (Player $player, $data) {
            str_replace('/', '', $data[1]);
            Loader::$editing[$player->getLowerCaseName()] = Loader::EDIT_ADDCOMMAND;
            Loader::$editvalues[$player->getLowerCaseName()] = $data;
        });
        $sender->sendForm($form);
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
