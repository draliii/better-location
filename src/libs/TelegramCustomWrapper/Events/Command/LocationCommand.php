<?php

namespace TelegramCustomWrapper\Events\Command;

use BetterLocation\BetterLocation;
use \Icons;
use TelegramCustomWrapper\TelegramHelper;
use Tracy\Debugger;
use Tracy\ILogger;

class LocationCommand extends Command
{
	/**
	 * LocationCommand constructor.
	 *
	 * @param $update
	 * @throws \Exception
	 */
	public function __construct($update) {
		parent::__construct($update);

		$result = null;
		try {
			$betterLocation = new BetterLocation(
				$this->update->message->location->latitude,
				$this->update->message->location->longitude,
				'Location'
			);
			$result = $betterLocation->generateBetterLocation();
		} catch (\Exception $exception) {
			$this->reply(sprintf('%s Unexpected error occured while processing location for Better location. Contact Admin for more info.', Icons::ERROR));
			Debugger::log($exception, ILogger::EXCEPTION);
			return;
		}
		if ($result) {
			$this->reply(
				TelegramHelper::MESSAGE_PREFIX . $result,
				['disable_web_page_preview' => true],
			);
			return;
		}
	}
}


