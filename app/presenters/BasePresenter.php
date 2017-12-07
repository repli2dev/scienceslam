<?php

use Muni\ScienceSlam\Utils\TexyFactory;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
	private $pageTitle;
	private $pageDescription;
	private $pageKeywords;

	/** @var string */
	public $googleAnalytics;
	/** @var string */
	public $facebookAnalytics;

	/** @var TexyFactory @inject */
	public $texyFactory;

	/** @var ISnippetControlFactory */
	protected $snippetControlFactory;

	/** @var string */
	protected $appDir;

	public function injectSnippetControlFactory(ISnippetControlFactory $snippetControlFactory) {
		$this->snippetControlFactory = $snippetControlFactory;
	}

	public function setPageTitle($input) {
		$this->pageTitle = $input;
	}
	public function setPageDescription($input) {
		$this->pageDescription = $input;
	}
	public function setPageKeywords($input) {
		$this->pageKeywords = $input;
	}
	public function startup()
	{
		parent::startup();
		$this->appDir = $this->context->getParameters()['appDir'];
	}

	protected function beforeRender() {
		parent::beforeRender();
		$this->template->pageTitle = (empty($this->pageTitle)) ? $this->context->parameters['page']['title'] : $this->pageTitle;
		$this->template->pageKeywords = (empty($this->pageKeywords)) ? $this->context->parameters['page']['keywords'] : $this->pageKeywords;
		$this->template->pageDescription = (empty($this->pageDescription)) ? $this->context->parameters['page']['description'] : $this->pageDescription;

		$this->template->admin = $this->user->isInRole('admin') || $this->user->isInRole('manager');
		$this->template->googleAnalytics = $this->googleAnalytics;
		$this->template->facebookAnalytics = $this->facebookAnalytics;
	}

	protected function createTemplate()
	{
		/** @var Template $template */
		$template = parent::createTemplate();
		$this->texyFactory->install($template->getLatte());
		return $template;
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

	protected function createComponentSnippet()
	{
		return $this->snippetControlFactory->create();
	}

}
