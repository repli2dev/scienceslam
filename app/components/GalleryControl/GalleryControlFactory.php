<?php

class GalleryControlFactory implements IGalleryControlFactory {

	/** @var int*/
	private $width;

	/** @var int */
	private $height;

	/** @var string */
	private $cachedPath;

	public function __construct($width, $height, $cachedPath)
	{
		$this->width = $width;
		$this->height = $height;
		$this->cachedPath = $cachedPath;
	}

	public function create()
	{
		return new GalleryControl($this->width, $this->height, $this->cachedPath);
	}
}