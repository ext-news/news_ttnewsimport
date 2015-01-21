<?php
namespace BeechIt\NewsTtnewsimport\Tests\Unit\Utility;

use BeechIt\NewsTtnewsimport\Utility\ArrayUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ComponentUsedTest
 *
 * @package Cyberhouse\Satisfaction\Tests\Unit\Domain\Validator
 */
class ArrayUtilityTest extends UnitTestCase {

	/**
	 * @dataProvider getArrayValueReturnsCorrectValueDataProvider
	 * @test
	 */
	public function getArrayValueReturnsCorrectValue($key, $expected) {
		$array = array(
			'level1_a' => array(
				'level2_b' => array(
					'key' => 'value123'
				)
			),
			'level1_b' => array(
				'level2_b' => array(
					'level3_b' => array(
						'key1' => 'value4',
						'key2' => 'value5'
					)
				)
			)
		);
		$this->assertEquals($expected, ArrayUtility::getArrayValue($array, $key));
	}

	public function getArrayValueReturnsCorrectValueDataProvider() {
		return array(
			array('level1_a.level2_b.key', 'value123'),
			array('level1_b.level2_b.level3_b.key1', 'value4'),
			array('level1_b.level2_b.level3_b.key2', 'value5'),
			array('level1_b.level2_b.level3_b.fo', NULL),
			array('level1_b.level2_b', array(
				'level3_b' => array(
					'key1' => 'value4',
					'key2' => 'value5'
				))),
			array('levelNotFound', NULL),
			array('', NULL),
		);
	}

}