<?php

namespace TelegramCustomWrapper\Events\Command;

class DebugCommand extends Command
{
	/**
	 * DebugCommand constructor.
	 *
	 * @param $update
	 * @throws \Exception
	 */
	public function __construct($update) {
		parent::__construct($update);

		$text = sprintf('%s <b>Debug</b> for @%s.', \Icons::COMMAND, TELEGRAM_BOT_NAME) . PHP_EOL;
		$text .= sprintf('This chat ID <code>%s</code>!', $this->getChatId()) . PHP_EOL;
		$text .= sprintf('Your user ID <code>%s</code>!', $this->getFromId()) . PHP_EOL;
		$this->reply($text);
	}
}