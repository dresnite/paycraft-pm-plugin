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

class DeletePlayerOnlineQueueTask extends ProviderAsyncTask {

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
		$this->delete("/webstore/queue/online/{$this->username}");
	}

	/**
	 * @throws Exception
	 */
	public function onCompletion(): void {
		$result = $this->getResult();

		$logger = $this->getPaycraft()->getLogger();

		if(!is_array($result)) {
			$logger->critical("Failed to run DeletePlayerOnlineQueueTask, the result wasn't valid");
			return;
		}

		if(array_key_exists("error", $result)) {
			$logger->error("Failed to delete player {$this->username} online queue, error: {$result["error"]}");
			return;
		}

		$logger->debug("Successfully removed {$this->username} online queue");
	}

}