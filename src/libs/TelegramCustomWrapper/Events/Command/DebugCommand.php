<?php

namespace TelegramCustomWrapper\Events\Command;

use Tracy\Debugger;
use Tracy\ILogger;
use Utils\General;

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
		$text .= PHP_EOL;
//		$currentCommitHash = $this->getGitHeadCommitHash();
		$currentCommitHash = 'e09569d762c3e7225310c23c46a7dc8d11952436';
		$now = new \DateTimeImmutable();

		$text .= sprintf('%s Repository version:', \Icons::INFO) . PHP_EOL;
		try {
			$commitsInfoRepo = $this->getGitCommitInfoFromApi();
			$text .= sprintf('Hash: <code>%s</code>', $commitsInfoRepo[0]->sha) . PHP_EOL;
			$committedInRepo = new \DateTimeImmutable($commitsInfoRepo->commit->author->date);
			$now = new \DateTimeImmutable();
			$diff = $now->getTimestamp() - $committedInRepo->getTimestamp();
			$text .= sprintf('Committed: %s (%s ago)', $committedInRepo->format(DATE_W3C), General::sToHuman($diff)) . PHP_EOL;
		} catch (\Exception $exception) {
			$text .= sprintf('Unable to get detailed info from Github API. Try again later.') . PHP_EOL;
			Debugger::log(sprintf('Unable to get info about newest commits from Github API: "%s"', $exception->getMessage()), ILogger::ERROR);
		}

		$text .= PHP_EOL;
		$text .= sprintf('%s Current version:', \Icons::INFO) . PHP_EOL;
		if ($currentCommitHash) {
			if (isset($commitsInfoRepo) && $commitsInfoRepo[0]->sha === $currentCommitHash) {
				$text .= sprintf('You are using the newest repository version %s', \Icons::CHECKED) . PHP_EOL;
			} else {
				$text .= sprintf('Hash: <code>%s</code>', $currentCommitHash) . PHP_EOL;
				try {
					$commitInfoCurrent = $this->getGitCommitInfoFromApi($currentCommitHash);
					$committedCurrent = new \DateTimeImmutable($commitInfoCurrent->commit->author->date);
					$diff = $now->getTimestamp() - $committedCurrent->getTimestamp();
					$text .= sprintf('Committed: %s (%s ago)', $committedCurrent->format(DATE_W3C), General::sToHuman($diff)) . PHP_EOL;

					if (isset($commitsInfoRepo)) {
						foreach ($commitsInfoRepo as $i => $commitInRepo) {
							if ($commitInRepo->sha === $currentCommitHash) {
								$diff = $committedInRepo->getTimestamp() - $committedCurrent->getTimestamp();
								$text .= sprintf('Behind %s commit(s). Diff between commits is %s.', $i,  General::sToHuman($diff)) . PHP_EOL;
								break;
							}
						}
					}
				} catch (\Exception $exception) {
					$text .= sprintf('Unable to get detailed info about current version from Github API. Try again later.') . PHP_EOL;
					Debugger::log(sprintf('Unable to get info about commit "%s" from Github API: "%s"', $currentCommitHash, $exception->getMessage()), ILogger::ERROR);
				}
			}
		} else {
			$text .= sprintf('%s Unable to get current version from .git folder.', \Icons::WARNING) . PHP_EOL;
		}

		$this->reply($text);
	}

	private function getGitHeadCommitHash(string $branch = 'master'): ?string {
		return trim(file_get_contents(sprintf('.git/refs/heads/%s', $branch))) ?? null;
	}

	/**
	 * @param string|null $hash
	 * @return mixed
	 * @throws \JsonException
	 */
	private function getGitCommitInfoFromApi(?string $hash = null) {
		if ($hash) {
			$url = sprintf('%s/repos/%s/commits/%s', REPOSITORY_URL_BASE_API, REPOSITORY_URL_PATH, $hash);
		} else {
			$url = sprintf('%s/repos/%s/commits', REPOSITORY_URL_BASE_API, REPOSITORY_URL_PATH);
		}
		$response = General::fileGetContents($url, [
			CURLOPT_USERAGENT => REPOSITORY_URL_PATH, // User agent is required: https://developer.github.com/v3/#user-agent-required
		]);
		return json_decode($response, false, 512, JSON_THROW_ON_ERROR);

	}
}