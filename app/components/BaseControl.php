<?php

/**
 * Basic control enhancing control's life cycle.
 *
 * @author Jan Drábek
 */
abstract class BaseControl extends Nette\Application\UI\Control {

	public function render() {
		// Add one stage of lifecycle
		$this->beforeRender();
	}

	/**
	 * Startup method is called just before rendering of this control
	 * Do not forget to call parent!
	 */
	protected function beforeRender() {}


	/**
	 * Returns path to directory of component
	 *
	 * @return string Path to directory
	 */
	protected function getPath() {
		$reflector = new \ReflectionClass(get_class($this));
		return dirname($reflector->getFileName());
	}

	/**
	 * Returns name of component (based on file name)
	 *
	 * @return string Name of component
	 */
	protected function getBaseName() {
		$reflector = new \ReflectionClass(get_class($this));
		return str_replace('.php','', basename($reflector->getFileName()));
	}
}
