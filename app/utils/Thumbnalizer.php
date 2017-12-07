<?php
namespace Muni\ScienceSlam\Utils;

use Nette\Application\BadRequestException;
use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Image;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Tracy\ILogger;

class Thumbnalizer
{

	/** @var string */
	private $galleryCacheDir;

	/** @var int */
	private $width;

	/** @var int */
	private $height;

	/** @var string */
	private $appDir;

	/** @var array */
	private $uploadsDirs;

	public function setAppDir($appDir)
	{
		$this->appDir = $appDir;
	}

	public function setUploadsDirs(array $uploadDirs)
	{
		$this->uploadsDirs = $uploadDirs;
	}

	public function setThumbnailDimensions($width, $height)
	{
		$this->width = $width;
		$this->height = $height;
	}

	public function setGalleryCacheDir($path)
	{
		$this->galleryCacheDir = $path;
	}

	public function thumbnalizeImage($path, $force = false)
	{
		$file = $path;
		// Check if in any upload dir
		$realPath = realpath($path);
		foreach ($this->uploadsDirs as $uploadsDir) {
			if (!Strings::startsWith($realPath, realpath($this->appDir . '/../'  . $uploadsDir))) {
				Debugger::log("Image [$path] outside uploads dirs.", ILogger::WARNING);
				return null;
			}
		}
		// Proceed to caching
		$cacheFile = Strings::replace($file, '#^' . preg_quote($this->appDir). '/../#u', $this->galleryCacheDir);
		$contentType = @mime_content_type($file); // mute as file can be also non-existing
		if (is_file($file) && in_array($contentType, ['image/jpeg', 'image/png', 'image/gif'], true)) {
			if ($force || !is_file($cacheFile) || filemtime($file) >= filemtime($cacheFile)) { // when original newer than cached
				try {
					FileSystem::createDir(dirname($cacheFile));
				} catch (IOException $exception) {
					Debugger::log($exception, ILogger::EXCEPTION);
					throw new BadRequestException('Cannot create thumbnail (cannot create dir).', 404);
				}
				$image = Image::fromFile($file);
				$image->resize($this->width, $this->height);
				$image->save($cacheFile);
				return true;
			}
			return false;
		}
		Debugger::log("No such image [$path].", ILogger::WARNING);
		return null;
	}
	public function thumbnalizeDirectory($path, $force = false)
	{
		if (!is_dir($path) || !is_readable($path)) {
			return 0;
		}
		$count = 0;
		$files = Finder::findFiles('*')->from($path);
		foreach ($files as $file => $fileInfo) {
			if ($this->thumbnalizeImage($file, $force) !== null) {
				$count++;
			}
		}
		return $count;
	}
}
