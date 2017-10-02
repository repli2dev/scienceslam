<?php

use Nette\Utils\Finder;
use Nette\Utils\Strings;

class GalleryControl extends VisualControl
{

	/** @var int */
	private $width;

	/** @var int */
	private $height;

	/**
	 * GalleryControl constructor.
	 * @param int $width
	 * @param int $height
	 */
	public function __construct($width, $height)
	{
		parent::__construct();
		$this->width = $width;
		$this->height = $height;
	}

	public function renderRender($path)
	{
		$this->template->path = $path;
		$this->template->width = $this->width;
		$this->template->height = $this->height;
		$this->template->images = static::getGalleryImages($path);
		$this->template->filesystemPath = __DIR__ . '/../../../';
		$this->render();
	}

	public static function getGalleryImages($path)
	{
		$filesystemPath = __DIR__ . '/../../../';
		$path = $filesystemPath . str_replace('../', '', $path);
		if (!file_exists($path) || !is_dir($path) || !is_readable($path)) {
			return [];
		}
		$images = Finder::findFiles('*')->filter(function (RecursiveDirectoryIterator $iterator) {
			/** @var SplFileInfo $file */
			$file = $iterator->current();
			return in_array(mime_content_type($file->getPathname()), ['image/jpeg', 'image/png', 'image/gif'], true);
		})->in($path);
		$temp = array_map(function (SplFileInfo $fileInfo) use ($filesystemPath) {
			return Strings::replace($fileInfo->getPathname(), '#^' . preg_quote($filesystemPath, '#') . '#', '');
		}, iterator_to_array($images->getIterator()));
		sort($temp, SORT_STRING | SORT_FLAG_CASE);
		return $temp;
	}

	public static function getGalleryTitleImage($path, $preferredPath = null)
	{
		$filesystemPath = __DIR__ . '/../../../';
		$preferredPathFull = $filesystemPath . str_replace('../', '', $preferredPath);
		if ($preferredPath && file_exists($preferredPathFull) && is_file($preferredPathFull) && in_array(mime_content_type($preferredPathFull), ['image/jpeg', 'image/png', 'image/gif'], true)) {
			return $preferredPath;
		}
		$temp = static::getGalleryImages($path);
		if (count($temp)) {
			return reset($temp);
		}
		return null;
	}


}