<?php

namespace xenialdan\ItemFrameCommands;

use pocketmine\lang\BaseLang;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase{

	public static $editing = [];
	public static $editvalues = [];

	/** @var BaseLang $baseLang */
	private $baseLang = null;

	const EDIT_NONE = 0;
	const EDIT_ADDCOMMAND = 1;
	const EDIT_REMOVECOMMAND = 2;
	const EDIT_REMOVEALLCOMMANDS = 3;
	const EDIT_LISTCOMMANDS = 4;
	const EDIT_SETNAME = 5;
	const EDIT_REMOVENAME = 6;
	const EDIT_SETITEM = 7;
	const EDIT_REMOVEITEM = 8;

	public function onEnable(){
		$lang = $this->getConfig()->get("language", BaseLang::FALLBACK_LANGUAGE);
		$this->baseLang = new BaseLang($lang, $this->getFile() . "resources/");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
	}

	/**
	 * @api
	 * @return BaseLang
	 */
	public function getLanguage(): BaseLang{
		return $this->baseLang;
	}
}