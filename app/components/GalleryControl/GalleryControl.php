<?php

use Muni\ScienceSlam\Utils\Thumbnalizer;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

class GalleryControl extends VisualControl
{

	/** @var int */
	private $width;

	/** @var int */
	private $height;

	/** @var string */
	private $cachedPath;

	/**
	 * GalleryControl constructor.
	 * @param int $width
	 * @param int $height
	 * @param string $cachedPath
	 */
	public function __construct($width, $height, $cachedPath)
	{
		parent::__construct();
		$this->width = $width;
		$this->height = $height;
		$this->cachedPath = $cachedPath;
	}

	public function renderRender($path)
	{
		$this->template->path = $path;
		$this->template->width = $this->width;
		$this->template->height = $this->height;
		$this->template->images = static::getGalleryImages($path, $this->cachedPath);
		$this->template->filesystemPath = __DIR__ . '/../../../';
		$this->render();
	}

	public static function getGalleryImages($path, $publicCachePath)
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
		$output = [];
		foreach ($images as $image) {
			$original = Strings::replace($image->getPathname(), '#^' . preg_quote($filesystemPath, '#') . '#', '');
			$cached = Strings::replace($original, '#^/#', $publicCachePath);
			if (!file_exists(__DIR__ . '/../../../' . $cached)) {
				continue;
			}
			$output[$cached] = $original;
		}
		asort($output, SORT_STRING | SORT_FLAG_CASE);
		return $output;
	}

	public static function getGalleryTitleImage($path, $publicCachePath, $preferredPath = null)
	{
		$filesystemPath = __DIR__ . '/../../../';
		$preferredPathFull = $filesystemPath . str_replace('../', '', $publicCachePath . $preferredPath);
		if ($preferredPath && file_exists($preferredPathFull) && is_file($preferredPathFull) && in_array(mime_content_type($preferredPathFull), ['image/jpeg', 'image/png', 'image/gif'], true)) {
			return $publicCachePath . $preferredPath;
		}
		$temp = static::getGalleryImages($path, $publicCachePath);
		if (count($temp)) {
			return key($temp);
		}
		return null;
	}
}
