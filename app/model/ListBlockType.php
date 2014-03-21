<?php
namespace Muni\ScienceSlam\Model;


class ListBlockType {

	const VERTICAL_TEXT = 1;
	const TEXT = 2;
	const IMAGE = 3;
	const REGISTRATION = 4;

	public static function getAll() {
		return array(
			self::VERTICAL_TEXT => "Centrovaný vertikální text",
			self::TEXT => "Blok s textem",
			self::IMAGE => "Obrázek s popiskem",
			self::REGISTRATION => "Registrace/lístky"
		);
	}

}