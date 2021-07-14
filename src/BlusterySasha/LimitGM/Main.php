<?php

declare(strict_types=1);

namespace BlusterySasha\LimitGM;

use EventListener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {
	public function onEnable() {
		$this->getLogger()->info("§cПлагин успешно запущен!");
		$this->getLogger()->info("§cАвтор плагина: §e§lvk.com/blusterysasha");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}
}