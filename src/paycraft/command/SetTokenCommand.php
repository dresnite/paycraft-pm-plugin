<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace paycraft\command;

use paycraft\Paycraft;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetTokenCommand extends Command {

	private Paycraft $paycraft;

	public function __construct(Paycraft $paycraft) {
		$this->paycraft = $paycraft;

		parent::__construct(
			"settoken",
			"Registers Paycraft token",
			"/settoken <token>"
		);

		$this->setPermission("paycraft.settoken");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("paycraft.settoken")) {
			$sender->sendMessage(TextFormat::RED . "You don't have permission to run this command");
			return;
		}

		if(!array_key_exists(0, $args)) {
			$sender->sendMessage("Usage: /settoken <token>");
			return;
		}

		$token = $args[0];

		$this->paycraft->setToken($token);

		$sender->sendMessage(TextFormat::GREEN . "You successfully updated Paycraft's token");
	}

}