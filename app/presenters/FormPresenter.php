<?php

use Nette\Application\UI;

/**
 * Presenter with forms
 */
class FormPresenter extends BasePresenter {


	protected function createComponentTicket($name) {
		$comp = $this->context->createComponentTicket($this, $name);
		$params = $this->context->parameters['form']['ticket'];
		$comp->setFrom($params['from']);
		$comp->setTo($params['to']);
		$comp->setMail($params['mail']);
		return $comp;
	}

	protected function createComponentSignup($name) {
		$comp = $this->context->createComponentSignup($this, $name);
		$params = $this->context->parameters['form']['signup'];
		$comp->setFrom($params['from']);
		$comp->setTo($params['to']);
		$comp->setMail($params['mail']);
		return $comp;
	}
}
