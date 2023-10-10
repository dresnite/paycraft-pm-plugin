<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace paycraft\event\listener;

use paycraft\Paycraft;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PaycraftListener implements Listener {

	private Paycraft $paycraft;

	public function __construct(Paycraft $paycraft) {
		$this->paycraft = $paycraft;
	}

	public function onPlayerJoin(PlayerJoinEvent $event): void {
		$this->paycraft->getProvider()->fetchPlayerQueue($event->getPlayer());
	}

}