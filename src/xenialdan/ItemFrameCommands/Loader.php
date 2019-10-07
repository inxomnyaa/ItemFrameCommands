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

    /** @var Loader */
    private static $instance = null;

    /**
     * Returns an instance of the plugin
     * @return Loader
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public function onLoad()
    {
        self::$instance = $this;
    }

    public function onEnable()
    {
		$lang = $this->getConfig()->get("language", BaseLang::FALLBACK_LANGUAGE);
        $this->baseLang = new BaseLang($lang, $this->getFile() . "resources" . DIRECTORY_SEPARATOR . "lang" . DIRECTORY_SEPARATOR);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("ItemFrameCommands", new Commands($this->getLanguage()->translateString("command.name"), $this->getLanguage()->translateString("command.desc")));
	}

	/**
	 * @api
	 * @return BaseLang
	 */
	public function getLanguage(): BaseLang{
		return $this->baseLang;
	}
}