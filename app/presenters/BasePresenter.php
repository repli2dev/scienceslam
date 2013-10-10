<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
	private $pageTitle;
	private $pageDescription;
	private $pageKeywords;

	public function setPageTitle($input) {
		$this->pageTitle = $input;
	}
	public function setPageDescription($input) {
		$this->pageDescription = $input;
	}
	public function setPageKeywords($input) {
		$this->pageKeywords = $input;
	}
	protected function beforeRender() {
		parent::beforeRender();
		$this->template->pageTitle = (empty($this->pageTitle)) ? $this->context->parameters['page']['title'] : $this->pageTitle;
		$this->template->pageKeywords = (empty($this->pageKeywords)) ? $this->context->parameters['page']['keywords'] : $this->pageKeywords;
		$this->template->pageDescription = (empty($this->pageDescription)) ? $this->context->parameters['page']['description'] : $this->pageDescription;
	}

	/**
	 * Check if user can see this item (used in menu)
	 * @return bool
	 */
	public function can($resource, $operation = \Nette\Security\IAuthorizator::ALL){
		$user = $this->getUser();
		if(!$user->isAllowed($resource, $operation)){
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Check for permission (all administration needs role master)
	 * Needs to be specified more precisely
	 */
	protected function perm($operation = \Nette\Security\IAuthorizator::ALL) {
		$user = $this->getUser();
		if($this->resource === NULL || !$user->isAllowed($this->resource, $operation)){
			$this->redirect('Admin:login');
		}
	}

}
