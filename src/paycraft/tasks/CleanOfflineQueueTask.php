<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace paycraft\tasks;

use paycraft\Paycraft;
use pocketmine\scheduler\Task;

class CleanOfflineQueueTask extends Task {

	private Paycraft $paycraft;

	public function __construct(Paycraft $paycraft) {
		$this->paycraft = $paycraft;
	}

	public function onRun(): void {
		if($this->paycraft->hasToken()) {
			$this->paycraft->getProvider()->fetchOfflineQueue();
		}
	}

}