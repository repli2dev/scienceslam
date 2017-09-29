<?php

class GalleryControlFactory implements IGalleryControlFactory {

	/** @var int*/
	private $width;

	/** @var int */
	private $height;

	public function __construct($width, $height)
	{
		$this->width = $width;
		$this->height = $height;
	}

	public function create()
	{
		return new GalleryControl($this->width, $this->height);
	}
}