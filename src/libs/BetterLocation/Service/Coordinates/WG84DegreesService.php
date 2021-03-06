<?php

declare(strict_types=1);

namespace BetterLocation\Service\Coordinates;

use BetterLocation\BetterLocation;
use BetterLocation\Service\Exceptions\InvalidLocationException;
use BetterLocation\Service\Exceptions\NotSupportedException;

final class WG84DegreesService extends AbstractService
{
	const RE_COORD = '([0-9]{1,3}\.[0-9]{1,20})';
	const NAME = 'WG84';

	/**
	 * @param float $lat
	 * @param float $lon
	 * @param bool $drive
	 * @return string
	 * @throws NotSupportedException
	 */
	public static function getLink(float $lat, float $lon, bool $drive = false): string {
		throw new NotSupportedException('Link for raw coordinates is not supported.');
	}

	public static function getRegex(): string {
		return self::RE_HEMISPHERE . self::RE_OPTIONAL_SPACE . self::RE_COORD . self::RE_HEMISPHERE . self::RE_SPACE_BETWEEN_COORDS . self::RE_HEMISPHERE . self::RE_OPTIONAL_SPACE . self::RE_COORD . self::RE_HEMISPHERE;
	}

	/**
	 * @param $text
	 * @return array<BetterLocation|InvalidLocationException>
	 */
	public static function findInText($text): array {
		$results = [];
		if (preg_match_all('/' . self::getRegex() . '/', $text, $matches)) {
			for ($i = 0; $i < count($matches[0]); $i++) {
				try {
					$results[] = self::parseCoords($matches[0][$i]);
				} catch (InvalidLocationException $exception) {
					$results[] = $exception;
				}
			}
		}
		return $results;
	}

	public static function isValid(string $input): bool {
		return !!preg_match('/^' . self::getRegex() . '$/', $input);
	}

	/**
	 * @param string $input
	 * @return BetterLocation
	 * @throws InvalidLocationException
	 */
	public static function parseCoords(string $input): BetterLocation {
		if (!preg_match('/^' . self::getRegex() . '$/', $input, $matches)) {
			throw new InvalidLocationException(sprintf('Input is not valid %s coordinates.', self::NAME));
		}
		// preg_match truncating empty values from the end in $matches array: https://stackoverflow.com/questions/43912763/php-can-preg-match-include-unmatched-groups#comment74860670_43912763
		$matches = array_pad($matches, 7, '');
		return self::processWG84(self::class, $matches);
	}
}
