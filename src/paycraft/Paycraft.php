<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace paycraft;

use paycraft\command\SetTokenCommand;
use paycraft\event\listener\PaycraftListener;
use paycraft\provider\Provider;
use paycraft\tasks\CleanOfflineQueueTask;
use pocketmine\plugin\PluginBase;

class Paycraft extends PluginBase {

	private Provider $provider;

	private ?string $token = null;

	protected function onEnable(): void {
		$this->provider = new Provider($this);
		$this->loadToken();

		$server = $this->getServer();
		$server->getPluginManager()->registerEvents(new PaycraftListener($this), $this);

		$server->getCommandMap()->register("paycraft", new SetTokenCommand($this));

		$this->getScheduler()->scheduleRepeatingTask(new CleanOfflineQueueTask($this), 20 * 60 * 3);
	}

	public function getProvider(): Provider {
		return $this->provider;
	}

	public function getToken(): ?string {
		return $this->token;
	}

	public function hasToken(): bool {
		return $this->token !== null;
	}

	public function setToken(?string $token): void {
		$this->token = $token;
	}

	private function loadToken(): void {
		$token = $this->getConfig()->get("token", "");

		$this->token = empty($token) ? null : $token;
	}

}