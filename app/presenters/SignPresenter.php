<?php

use Nette\Application\UI;

/**
 * Presenter for speakers signing up
 */
class SignPresenter extends BasePresenter {

	/** @var ISignupFormFactory */
	private $signupFormFactory;

	public function injectSignupFormFactory(ISignupFormFactory $factory) {
		$this->signupFormFactory = $factory;
	}

	protected function createComponentSignup($name) {
		$comp = $this->signupFormFactory->create();
		$params = $this->context->parameters['form']['signup'];
		$comp->setMail($params['mail']);
		return $comp;
	}
}
