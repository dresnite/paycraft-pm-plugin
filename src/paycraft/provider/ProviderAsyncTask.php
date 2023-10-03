<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace paycraft\provider;

use CurlHandle;
use Exception;
use paycraft\Paycraft;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;

abstract class ProviderAsyncTask extends AsyncTask {

	protected const API_URL = "https://paycraft.gg";

	protected string $token;

	public function __construct(Provider $provider) {
		$paycraft = $provider->getPaycraft();

		if(!$paycraft->hasToken()) {
			throw new AssumptionFailedError("Failed to create task, the token is not loaded");
		}

		$this->token = $paycraft->getToken();
	}

	/**
	 * @throws Exception
	 */
	protected function get(string $route): void {
		$ch = $this->getCurlSession(self::API_URL . $route, [
			"Content-Type: application/json"
		]);

		$this->executeCurlHandle($ch);
	}

	/**
	 * @param string $route
	 * @param array  $body
	 *
	 * @throws Exception
	 */
	protected function post(string $route, array $body): void {
		$ch = $this->getCurlSession(self::API_URL . $route, [
			"Content-Type: application/json"
		]);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

		$this->executeCurlHandle($ch);
	}

	/**
	 * @param string $route
	 * @param array  $body
	 *
	 * @throws Exception
	 */
	protected function patch(string $route, array $body): void {
		$ch = $this->getCurlSession(self::API_URL . $route, [
			"Content-Type: application/json"
		]);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

		$this->executeCurlHandle($ch);
	}

	/**
	 * @param string $route
	 * @param array  $body
	 *
	 * @throws Exception
	 */
	protected function delete(string $route, array $body = []): void {
		$ch = $this->getCurlSession(self::API_URL . $route, [
			"Content-Type: application/json"
		]);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

		$this->executeCurlHandle($ch);
	}

	/**
	 * @return Plugin|Paycraft
	 * @throws Exception
	 */
	protected function getPaycraft(): Plugin|Paycraft {
		$plugin = Server::getInstance()->getPluginManager()->getPlugin("Paycraft");
		if($plugin === null) {
			throw new Exception("Tried to execute an async task while the Buycraft plugin is disabled");
		}
		return $plugin;
	}

	protected function getCurlSession(string $url, array $headers = []): CurlHandle|bool {
		$session = curl_init($url);

		curl_setopt($session, CURLOPT_HTTPHEADER, [
			...$headers,
			"Authorization: Bearer {$this->token}"
		]);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($session, CURLOPT_TIMEOUT, 5);

		return $session;
	}

	/**
	 * @param CurlHandle $curl
	 *
	 * @throws Exception
	 */
	private function executeCurlHandle(CurlHandle $curl): void {
		$result = curl_exec($curl);

		if($result === false) {
			throw new Exception("Query error: " . curl_error($curl));
		}

		curl_close($curl);

		$this->setResult(json_decode($result, true));
	}

}