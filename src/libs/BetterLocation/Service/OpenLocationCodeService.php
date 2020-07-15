<?php

declare(strict_types=1);

namespace BetterLocation\Service;

use BetterLocation\BetterLocation;
use OpenLocationCode\OpenLocationCode;

final class OpenLocationCodeService extends AbstractService
{
	const LINK = 'https://plus.codes/';

	const DEFAULT_CODE_LENGTH = 12;

	/**
	 * @param float $lat
	 * @param float $lon
	 * @param bool $drive
	 * @return string
	 * @throws \Exception
	 */
	public static function getLink(float $lat, float $lon, bool $drive = false): string {
		if ($drive) {
			throw new \InvalidArgumentException('Drive link is not implemented.');
		} else {
			$plusCode = OpenLocationCode::encode($lat, $lon, self::DEFAULT_CODE_LENGTH);
			return self::LINK . $plusCode;
		}
	}

	public static function isValid(string $input): bool {
		return self::isUrl($input) || self::isCode($input);
	}

	/**
	 * @param string $plusCodeInput
	 * @return BetterLocation
	 * @throws \Exception
	 */
	public static function parseCoords(string $plusCodeInput) {
		if (self::isUrl($plusCodeInput)) {
			$coords = self::parseUrl($plusCodeInput);
			return new BetterLocation(
				$coords[0],
				$coords[1],
				sprintf('<a href="%s">(OLC)</a>: ', $plusCodeInput) // @TODO would be nice to return detected OLC code
			);
		} else if (self::isCode($plusCodeInput)) {  // at least two characters, otherwise it is probably /s/hort-version of link
			$coords = OpenLocationCode::decode($plusCodeInput);
			return new BetterLocation(
				$coords['latitudeCenter'],
				$coords['longitudeCenter'],
				sprintf('<a href="%s">(OLC)</a> <code>%s</code>: ', self::getLink($coords['latitudeCenter'], $coords['longitudeCenter']), $plusCodeInput),
			);
		} else {
			throw new \Exception('Unable to get coords from OpenLocationCode.');
		}
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	public static function isUrl(string $url): bool {
		// https://plus.codes/8FXP74WG+XHW
		if (substr($url, 0, mb_strlen(self::LINK)) === self::LINK) {
			$plusCode = str_replace(self::LINK, '', $url);
			return self::isValid($plusCode);
		}
		return false;
	}

	/**
	 * @param string $plusCode
	 * @return bool
	 *
	 */
	public static function isCode(string $plusCode): bool {
		return OpenLocationCode::isValid($plusCode);
	}

	/**
	 * @TODO query parameters should have higher priority than hash params
	 *
	 * @param string $url
	 * @return array|null
	 * @throws \Exception
	 */
	public static function parseUrl(string $url): ?array {
		$plusCode = str_replace(self::LINK, '', $url);
		$coords = OpenLocationCode::decode($plusCode);
		return [
			$coords['latitudeCenter'],
			$coords['longitudeCenter'],
		];
	}
}