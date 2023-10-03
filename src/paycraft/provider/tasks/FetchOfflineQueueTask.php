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
use paycraft\provider\ProviderAsyncTask;
use pocketmine\console\ConsoleCommandSender;

class FetchOfflineQueueTask extends ProviderAsyncTask {

	/**
	 * @return void
	 * @throws Exception
	 */
	public function onRun(): void {
		$this->get("/webstore/queue/offline");
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
			$logger->critical("Failed to run FetchOfflineQueueTask, the result wasn't valid.");
			return;
		}

		if(array_key_exists("error", $result)) {
			$logger->error("Failed to fetch offline queue, error: {$result["error"]}");
			return;
		}

		$server = $paycraft->getServer();
		foreach($result["queue"] as $queued) {
			$server->dispatchCommand(
				new ConsoleCommandSender($server, $server->getLanguage()),
				str_replace("{player}", $queued["target"], $queued["command"])
			);
		}

		$paycraft->getProvider()->deleteOfflineQueue();

		$logger->debug("Successfully fetched queue offfline commands");
	}

}