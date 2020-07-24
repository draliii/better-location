<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \BetterLocation\Service\MapyCzService;
use \BetterLocation\Service\Exceptions\InvalidLocationException;

require_once __DIR__ . '/../src/config.php';

final class MapyCzServiceTest extends TestCase
{
	/** @noinspection PhpUnhandledExceptionInspection */
	public function testMapyCzXY(): void {
		$this->assertEquals('50.069524, 14.450824', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.4508239&y=50.0695244&z=15')->__toString());
		$this->assertEquals('50.069524, 14.450824', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?y=50.0695244&x=14.4508239&z=15')->__toString());
	}

	/**
	 * ID parameter is in coordinates format
	 *
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function testValidCoordinatesMapyCzId(): void {
		$this->assertEquals('50.077886, 14.371990', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.3985113&y=50.0696783&z=15&source=coor&id=14.371989590930184%2C50.07788610486586')->__toString());
		$this->assertEquals('7.731071, -80.551001', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=-80.5308310&y=7.7192491&z=15&source=coor&id=-80.55100118168951%2C7.731071318967728')->__toString());
		$this->assertEquals('65.608884, -168.088871', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=-168.0916515&y=65.6066015&z=15&source=coor&id=-168.08887063564356%2C65.60888429109842')->__toString());
		$this->assertEquals('-1.138011, 9.034823', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=8.9726814&y=-1.2094073&z=11&source=coor&id=9.034822831066833%2C-1.1380111329277875')->__toString());
	}

	/**
	 * ID parameter is in INVALID latitude coordinates format
	 */
	public function testInvalidLatCoordinatesMapyCzId(): void {
		$this->expectException(InvalidLocationException::class);
		$this->expectExceptionMessage('Latitude coordinate must be between or equal from -90 to 90 degrees.');
		$this->assertEquals('50.077886, 14.371990', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.3985113&y=50.0696783&z=15&source=coor&id=14.371989590930184%2C150.07788610486586')->__toString());
	}

	/**
	 * ID parameter is in INVALID longitude coordinates format
	 */
	public function testInvalidLonCoordinatesMapyCzId(): void {
		$this->expectException(InvalidLocationException::class);
		$this->expectExceptionMessage('Longitude coordinate must be between or equal from -180 to 180 degrees.');
		MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.3985113&y=50.0696783&z=15&source=coor&id=190.371989590930184%2C50.07788610486586');
	}

	/**
	 * Translate MapyCZ place ID to coordinates
	 *
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function testValidMapyCzId(): void {
		if (!is_null(MAPY_CZ_DUMMY_SERVER_URL)) {
			$this->assertEquals('50.073784, 14.422105', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.4508239&y=50.0695244&z=15&source=base&id=2111676')->__toString());
			$this->assertEquals('50.084007, 14.440339', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.4527551&y=50.0750056&z=15&source=pubt&id=15308193')->__toString());
			$this->assertEquals('50.084747, 14.454012', MapyCzService::parseCoords('https://mapy.cz/zakladni?x=14.4651576&y=50.0796325&z=15&source=firm&id=468797')->__toString());
			$this->assertEquals('50.093312, 14.455159', MapyCzService::parseCoords('https://mapy.cz/zakladni?x=14.4367048&y=50.0943640&z=15&source=traf&id=15659817')->__toString());
			$this->assertEquals('50.106624, 14.366203', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.3596717&y=50.0997874&z=15&source=base&id=1833337')->__toString()); // area
			// some other places than Czechia (source OSM)
			$this->assertEquals('49.444980, 11.109055', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=11.0924687&y=49.4448356&z=15&source=osm&id=112448327')->__toString());
			// negative coordinates (source OSM)
			$this->assertEquals('54.766918, -101.873729', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=-101.8754373&y=54.7693842&z=15&source=osm&id=1000536418')->__toString());
			$this->assertEquals('-18.917167, 47.535756', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=47.5323757&y=-18.9155159&z=16&source=osm&id=1040985945')->__toString());
			$this->assertEquals('-45.870330, -67.507560', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=-67.5159386&y=-45.8711989&z=15&source=osm&id=17164289')->__toString());
		}
	}

	/**
	 * @see MapyCzServiceTest::testValidMapyCzId() exactly the same just shortened links
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function testValidMapyCzIdShortUrl(): void {
		if (!is_null(MAPY_CZ_DUMMY_SERVER_URL)) {
			$this->assertEquals('50.533111, 16.155906', MapyCzService::parseCoords('https://en.mapy.cz/s/devevemoje')->__toString());
			$this->assertEquals('50.084007, 14.440339', MapyCzService::parseCoords('https://en.mapy.cz/s/degogalazo')->__toString());
			$this->assertEquals('50.084747, 14.454012', MapyCzService::parseCoords('https://en.mapy.cz/s/cavukepuba')->__toString());
			$this->assertEquals('50.093312, 14.455159', MapyCzService::parseCoords('https://en.mapy.cz/s/fuvatavode')->__toString());
			$this->assertEquals('50.106624, 14.366203', MapyCzService::parseCoords('https://en.mapy.cz/s/gesaperote')->__toString()); // area
			// some other places than Czechia (source OSM)
			$this->assertEquals('49.444980, 11.109055', MapyCzService::parseCoords('https://en.mapy.cz/s/hozogeruvo')->__toString());
			// negative coordinates (source OSM)
			$this->assertEquals('54.766918, -101.873729', MapyCzService::parseCoords('https://en.mapy.cz/s/dasorekeja')->__toString());
			$this->assertEquals('-18.917167, 47.535756', MapyCzService::parseCoords('https://en.mapy.cz/s/maposedeso')->__toString());
			$this->assertEquals('-45.870330, -67.507560', MapyCzService::parseCoords('https://en.mapy.cz/s/robelevuja')->__toString());
		}
	}

	/**
	 * Translate MapyCZ place ID to coordinates
	 *
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function testInvalidMapyCzId(): void {
		if (!is_null(MAPY_CZ_DUMMY_SERVER_URL)) {
			$this->expectExceptionMessage('Unable to get valid coordinates from point ID "1234".');
			MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.4508239&y=50.0695244&z=15&source=base&id=1234');
		}
	}

	/** @noinspection PhpUnhandledExceptionInspection */
	public function testMapyCzIdFallback(): void {
		// Method is using constant from local config, which can't be changed, so "fake" place ID and put some non-numeric char there which is invalid and it will run fallback to X/Y
		// @TODO refactor this to be able to run true tests
		$this->assertEquals('50.069524, 14.450824', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?x=14.4508239&y=50.0695244&z=15&source=base&id=2111676a')->__toString());
		$this->assertEquals('50.069524, 14.450824', MapyCzService::parseCoords('https://en.mapy.cz/zakladni?y=50.0695244&x=14.4508239&z=15&source=base&id=2111676a')->__toString());
	}

}