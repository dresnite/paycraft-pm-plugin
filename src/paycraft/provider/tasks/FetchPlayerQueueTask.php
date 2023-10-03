<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace paycraft\provider\tasks;

use Exception;
use paycraft\provider\Provider;
use paycraft\provider\ProviderAsyncTask;
use pocketmine\console\ConsoleCommandSender;

class FetchPlayerQueueTask extends ProviderAsyncTask {

	private string $username;

	public function __construct(Provider $provider, string $username) {
		$this->username = $username;

		parent::__construct($provider);
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function onRun(): void {
		$this->get("/webstore/queue/online/{$this->username}");
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function onCompletion(): void {
		$result = $this->getResult();

		$paycraft = $this->getPaycraft();
		$logger = $paycraft->getLogger();

		if(!is_array($result)) {
			$logger->critical("Failed to run FetchPlayerQueueTask, the result wasn't valid");
			return;
		}

		if(!array_key_exists("queue", $result)) {
			$error = $result["error"] ?? "Unknown";
			$logger->error("Failed to obtain online queue for {$this->username}, error: {$error}");
			return;
		}

		$server = $paycraft->getServer();

		$target = $server->getPlayerExact($this->username);

		if($target === null) {
			$logger->debug("Failed to run {$this->username} commands because they disconnected");
			return;
		}

		foreach($result["queue"] as $queued) {
			$server->dispatchCommand(
				new ConsoleCommandSender($server, $server->getLanguage()),
				str_replace("{player}", $this->username, $queued["command"])
			);
		}

		$paycraft->getProvider()->deletePlayerOnlineQueue($target);
	}

}