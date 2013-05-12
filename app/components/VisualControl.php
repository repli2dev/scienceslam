<?php

use Nette\Templating\ITemplate;

/**
 * Basic control (~ GUI component). It creates basic capability for visual components.
 * and handle basic templating.
 *
 * @author Jan Drábek
 */
abstract class VisualControl extends BaseControl {

	private $view;

	/** @inheritdoc */
	public function render() {
		parent::render();
		// Automatic rendering in case of no render method
		$this->getTemplate()->render();
	}

	public function beforeRender() {
		parent::beforeRender();
	}

	/** @inheritdoc */
	protected function createTemplate($class = NULL) {
		$template = $this->getControlTemplate($this->getBaseName(),$class);
		return $template;
	}

	protected function getView() {
		return $this->view;
	}

	protected function isAjax() {
		return $this->getPresenter()->isAjax();
	}

	protected function setView($name) {
		$this->view = $name;
		$this->template->setFile($this->getPath(). '/'. $name . '.latte');
	}

	/**
	 * Create and return template with given name from control's class directory
	 *
	 * @param string $name Filename of template without extensions
	 * @param string $class Class name of template to use (e.g. FileTemplate)
	 * @return ITemplate
	 */
	protected function getControlTemplate($name, $class = NULL) {
		$template = parent::createTemplate($class);
		$template->setFile($this->getPath(). '/'. $name . '.latte');
		//$template->registerHelperLoader('\Edookit\Utils\TemplateHelpers::loader');
		return $template;
	}
}
