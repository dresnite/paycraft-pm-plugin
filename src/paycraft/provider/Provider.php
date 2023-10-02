<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace paycraft\provider;

use paycraft\Paycraft;
use paycraft\provider\tasks\DeleteOfflineQueueTask;
use paycraft\provider\tasks\DeletePlayerOnlineQueueTask;
use paycraft\provider\tasks\FetchOfflineQueueTask;
use paycraft\provider\tasks\FetchPlayerQueueTask;
use pocketmine\player\Player;

class Provider {

	private Paycraft $paycraft;

	public function __construct(Paycraft $paycraft) {
		$this->paycraft = $paycraft;
	}

	public function getPaycraft(): Paycraft {
		return $this->paycraft;
	}

	public function fetchPlayerQueue(Player $player): void {
		$this->scheduleTask(new FetchPlayerQueueTask($this, $player->getName()));
	}

	public function deletePlayerOnlineQueue(Player $player): void {
		$this->scheduleTask(new DeletePlayerOnlineQueueTask($this, $player->getName()));
	}

	public function fetchOfflineQueue(): void {
		$this->scheduleTask(new FetchOfflineQueueTask($this));
	}

	public function deleteOfflineQueue(): void {
		$this->scheduleTask(new DeleteOfflineQueueTask($this));
	}

	private function scheduleTask(ProviderAsyncTask $task): void {
		$this->paycraft->getServer()->getAsyncPool()->submitTask($task);
	}

}