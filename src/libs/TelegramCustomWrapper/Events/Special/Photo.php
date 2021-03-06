<?php

namespace TelegramCustomWrapper\Events\Special;

use \Icons;
use BetterLocation\BetterLocation;
use TelegramCustomWrapper\TelegramHelper;
use Tracy\Debugger;
use Tracy\ILogger;

class Photo extends \TelegramCustomWrapper\Events\Special\Special
{
	/**
	 * PhotoCommand constructor.
	 *
	 * @param $update
	 * @throws \Exception
	 */
	public function __construct($update) {
		parent::__construct($update);

		$result = '';
		try {
			$betterLocations = BetterLocation::generateFromTelegramMessage(
				$this->update->message->caption,
				$this->update->message->caption_entities,
			);
			foreach ($betterLocations as $betterLocation) {
				$result .= $betterLocation->generateBetterLocation();
			}
		} catch (\Exception $exception) {
			$this->reply(sprintf('%s Unexpected error occured while processing photo caption for Better location. Contact Admin for more info.', Icons::ERROR));
			Debugger::log($exception, ILogger::EXCEPTION);
			return;
		}
		if ($result) {
			$this->reply(
				TelegramHelper::MESSAGE_PREFIX . $result,
				['disable_web_page_preview' => true],
			);
			return;
		} else if ($this->isPm()) {
			$this->reply('Thanks for the photo in PM! But I\'m not sure, what to do... If you want to process location from EXIF, you have to send <b>uncompressed</b> photo.');
		}
	}
}


