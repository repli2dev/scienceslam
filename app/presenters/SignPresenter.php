<?php

use Muni\ScienceSlam\Model\Page;
use Nette\Application\UI;

/**
 * Presenter for speakers signing up
 */
class SignPresenter extends BasePresenter {

	/** @var ISignupFormFactory */
	private $signupFormFactory;
	/** @var Page */
	private $pageDAO;

	public function injectSignupFormFactory(ISignupFormFactory $factory) {
		$this->signupFormFactory = $factory;
	}
	public function injectPageDAO(Page $pageDAO) {
		$this->pageDAO = $pageDAO;
	}

	public function actionUp() {
		$this->template->page = $this->pageDAO->findByUrlAndEventId('signup', null);
	}

	protected function createComponentSignup($name) {
		$comp = $this->signupFormFactory->create();
		$params = $this->context->parameters['form']['signup'];
		$comp->setMail($params['mail']);
		return $comp;
	}
}
