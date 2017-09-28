<?php

class SnippetControl extends VisualControl
{
	/** @var \Muni\ScienceSlam\Model\Snippet */
	private $snippetDAO;

	public function __construct(\Muni\ScienceSlam\Model\Snippet $snippet)
	{
		parent::__construct();
		$this->snippetDAO = $snippet;
	}

	public function renderRender($key)
	{
		$this->template->content = '';
		$snippet = $this->snippetDAO->getByKey($key);
		if ($snippet) {
			$this->template->content = $snippet->content;
		}
		$this->render();
	}
}