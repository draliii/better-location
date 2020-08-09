<?php

namespace TelegramCustomWrapper\Events\Command;

class FavouritesCommand extends Command
{
	const CMD = '/favourites';

	/**
	 * FavouriteCommand constructor.
	 *
	 * @param $update
	 * @throws \Exception
	 */
	public function __construct($update) {
		parent::__construct($update);
		if ($this->isPm()) {
			$this->processFavouritesList(false);
		} else {
			$this->reply(sprintf('%s Command <code>%s</code> is available only in private message, open @%s.', \Icons::ERROR, self::CMD, TELEGRAM_BOT_NAME));
		}
	}
}