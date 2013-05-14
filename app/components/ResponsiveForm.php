<?php

use Nette\Application\UI\Form;

class ResponsiveForm extends Form {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$renderer = $this->getRenderer();

		$renderer->wrappers['controls']['container'] = 'dl';
		$renderer->wrappers['pair']['container'] = 'div';
		$renderer->wrappers['label']['container'] = 'dt';
		$renderer->wrappers['control']['container'] = 'dd';
	}

	public function appendClass($newClass) {
		$attrs = $this->getElementPrototype()->attrs;
		if(isSet($attrs['class'])) {
			$this->getElementPrototype()->class($attrs['class'] . ' '. $newClass);
		} else {
			$this->getElementPrototype()->class($newClass);
		}
	}
}