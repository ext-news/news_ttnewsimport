<?php
namespace BeechIt\NewsTtnewsimport\Utility;


class ArrayUtility {

	/**
	 * @param array $array
	 * @param string $key
	 * @return array|string|null
	 */
	static public function getArrayValue(array $array, $key) {
		$keys = explode('.', $key);
		$currentArray = $array;
		for ($i = 0; $i < count($keys); $i++) {
			if (isset($currentArray[$keys[$i]])) {
				$currentArray = $currentArray[$keys[$i]];
			} else {
				return NULL;
			}
		}

		return $currentArray;
	}
}