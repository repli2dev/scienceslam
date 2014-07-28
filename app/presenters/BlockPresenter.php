<?php

use Nette\Application\UI;

class BlockPresenter extends BasePresenter {

	protected $resource = 'block';

	/** @var \Muni\ScienceSlam\Model\Block */
	private $blockDAO;
	/** @var \Muni\ScienceSlam\Model\Page */
	private $pageDAO;
	/** @var \Muni\ScienceSlam\Model\Event */
	private $eventDAO;

	public function injectBlockDAO(\Muni\ScienceSlam\Model\Block $blockDAO) {
		$this->blockDAO = $blockDAO;
	}
	public function injectPageDAO(\Muni\ScienceSlam\Model\Page $pageDAO) {
		$this->pageDAO = $pageDAO;
	}
	public function injectEventDAO(\Muni\ScienceSlam\Model\Event $eventDAO) {
		$this->eventDAO = $eventDAO;
	}

	public function startup() {
		parent::startup();
		$this->perm();
	}

	public function actionAdd($pageId) {
		$data = $this->pageDAO->find($pageId);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException;
		}
		$this->template->page = $data;
	}

	public function actionEdit($blockId) {
		$data = $this->blockDAO->find($blockId);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$form = $this->getComponent('editForm');
		$use = iterator_to_array($data);
		$use['layout'] = $use['block_type_id'];
		if($use['layout'] == \Muni\ScienceSlam\Model\ListBlockType::TEXT) {
			$use[\Muni\ScienceSlam\Model\ListBlockType::TEXT]['content'] = $use['param1'];
		}
		if($use['layout'] == \Muni\ScienceSlam\Model\ListBlockType::VERTICAL_TEXT) {
			$use[\Muni\ScienceSlam\Model\ListBlockType::VERTICAL_TEXT]['content'] = $use['param1'];
		}
		if($use['layout'] == \Muni\ScienceSlam\Model\ListBlockType::IMAGE) {
			$use[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['label'] = $use['param1'];
			$use[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['label2'] = $use['param2'];
			$use[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['style'] = $use['style'];
		}
		$form->setDefaults($use);
	}
	public function actionDelete($pageId) {
		$data = $this->blockDAO->find($pageId);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$this->template->data = $data;
	}

	protected function createComponentAddForm() {
		$form = $this->prepareForm();
		$form->addSubmit('send', 'Přidat blok');
		$form->onSuccess[] = $this->addFormSucceeded;
		return $form;
	}

	public function addFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();

		$output = array();
		$output['block_type_id'] = $layout = $values['layout'];
		if($layout == \Muni\ScienceSlam\Model\ListBlockType::TEXT) {
			$output['param1'] = $values[\Muni\ScienceSlam\Model\ListBlockType::TEXT]['content'];
		}
		if($layout == \Muni\ScienceSlam\Model\ListBlockType::VERTICAL_TEXT) {
			$output['param1'] = $values[\Muni\ScienceSlam\Model\ListBlockType::VERTICAL_TEXT]['content'];
		}
		if($layout == \Muni\ScienceSlam\Model\ListBlockType::IMAGE) {
			$output['param1'] = $values[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['label'];
			$output['param2'] = $values[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['label2'];
			$output['style'] = $values[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['style'];
		}
		$output['classes'] = $values['classes'];
		$output['link'] = $values['link'];
		$output['weight'] = $values['weight'];
		$output['size'] = $values['size'];
		$output['hidden'] = $values['hidden'];
		$output['page_id'] = $this->getParameter('pageId');
		// Add checks for dates etc.

		$row = $this->blockDAO->create();
		$row->addAll($output);
		try {
			$this->blockDAO->save($row);
		} catch(PDOException $ex) {
			throw $ex;
			return;
		}
		$page = $this->pageDAO->find($this->getParameter('pageId'));
		$event = $this->eventDAO->find($page->event_id);
		if($event === FALSE) {
			$eventUrl = null;
		} else {
			$eventUrl = $event->url;
		}
		$this->flashMessage('Blok byla úspěšně přidán.', 'success');
		$this->redirect('Page:show', $eventUrl, $page->url);
	}

	protected function createComponentEditForm() {
		$form = $this->prepareForm();
		$form->addSubmit('send', 'Upravit Blok');
		$form->onSuccess[] = $this->editFormSucceeded;
		return $form;
	}

	public function editFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();

		$output = array();
		$output['block_type_id'] = $layout = $values['layout'];
		if($layout == \Muni\ScienceSlam\Model\ListBlockType::TEXT) {
			$output['param1'] = $values[\Muni\ScienceSlam\Model\ListBlockType::TEXT]['content'];
		}
		if($layout == \Muni\ScienceSlam\Model\ListBlockType::VERTICAL_TEXT) {
			$output['param1'] = $values[\Muni\ScienceSlam\Model\ListBlockType::VERTICAL_TEXT]['content'];
		}
		if($layout == \Muni\ScienceSlam\Model\ListBlockType::IMAGE) {
			$output['param1'] = $values[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['label'];
			$output['param2'] = $values[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['label2'];
			$output['style'] = $values[\Muni\ScienceSlam\Model\ListBlockType::IMAGE]['style'];
		}
		$output['classes'] = $values['classes'];
		$output['link'] = $values['link'];
		$output['weight'] = $values['weight'];
		$output['size'] = $values['size'];
		$output['hidden'] = $values['hidden'];
		// Add checks for dates etc.

		$row = $this->blockDAO->find($this->getParameter('blockId'));
		$row = \JanDrabek\Database\WatchingActiveRow::fromActiveRow($row);
		$row->addAll($output);
		try {
			$this->blockDAO->save($row);
		} catch(PDOException $ex) {
			throw $ex;
			return;
		}
		$page = $this->pageDAO->find($row->page_id);
		$event = $this->eventDAO->find($page->event_id);
		if($event === FALSE) {
			$eventUrl = null;
		} else {
			$eventUrl = $event->url;
		}
		$this->flashMessage('Blok byla úspěšně upraven.', 'success');
		$this->redirect('Page:show', $eventUrl, $page->url);
	}

	protected function createComponentDeleteForm() {
		$form = new UI\Form();
		$form->addSubmit('yes', 'Ano');
		$form->addSubmit('no', 'Ne');
		$form->onSuccess[] = $this->deleteFormSucceeded;
		return $form;
	}

	public function deleteFormSucceeded(\Nette\Forms\Form $form) {
		$block = $this->blockDAO->find($this->getParameter('pageId'));
		if($form['yes']->isSubmittedBy()) {
			$this->blockDAO->delete($this->getParameter('pageId'));
			$this->flashMessage('Blok byl úspěšně smazán.', 'success');
		}
		$page = $this->pageDAO->find($block->page_id);
		$event = $this->eventDAO->find($page->event_id);
		if($event === FALSE) {
			$eventUrl = null;
		} else {
			$eventUrl = $event->url;
		}
		$this->redirect('Page:show', $eventUrl, $page->url);
	}

	private function prepareForm() {
		$form = new UI\Form;
		$form->getElementPrototype()->class('wide');
		$form->addGroup('Typ');
		$form->addSelect('layout', 'Rozložení', \Muni\ScienceSlam\Model\ListBlockType::getAll())
			->setPrompt('--- Vyberte ---')
			->setRequired('Vyberte, prosím, rozložení bloku.');
		$form->addSelect('size', 'Velikost', array('1x1' => '1x1', '2x1' => '2x1'))
			->setRequired('Vyberte, prosím, velikost bloku.');

		$form->addContainer('general');
		$form->addGroup('Společená nastavení');
		$form->addRadioList('classes', 'Pozadí', array(null => 'Žádné', 'back_1x1_a' => 'back_1x1_a', 'back_1x1_b' => 'back_1x1_b', 'back_1x1_c' => 'back_1x1_c', 'back_2x1_c' => 'back_2x1_c'));
		$form->addText('link', 'Odkaz')
			->setOption('description', 'Absolutní/relativní, celý blok');
		$form->addText('weight', 'Váha')
			->addRule(\Nette\Forms\Form::INTEGER, 'Váha musí být celé číslo.')
			->setDefaultValue(0);
		$form->addCheckbox('hidden', 'Skryto');

		$form->addGroup('Rozložení: Vertikálně centrovaný text');
		$c = $form->addContainer(\Muni\ScienceSlam\Model\ListBlockType::VERTICAL_TEXT);

		$c->addTextArea('content', 'Obsah', 48, 5);

		$form->addGroup('Rozložení: Text');
		$c = $form->addContainer(\Muni\ScienceSlam\Model\ListBlockType::TEXT);
		$c->addTextArea('content', 'Obsah', 48, 5);

		$form->addGroup('Rozložení: Obrázek');
		$c = $form->addContainer(\Muni\ScienceSlam\Model\ListBlockType::IMAGE);
		$c->addText('label', 'Nadpisek');
		$c->addText('label2', 'Krátký popis');
		$c->addSelect('style', 'Obrázek na pozadí', $this->getImages(), 10)
			->setPrompt('--- Vyberte ---');

		$form->setCurrentGroup(null);
		return $form;
	}

	private function getImages() {
		$dirs = $this->context->parameters['uploads']['dirs'];
		$finder = \Nette\Utils\Finder::findFiles(array('*.jpg', '*.png'))->from($dirs)->exclude('gallery');
		$output = array();

		foreach($finder as $file) {
			$output[$file->getPath().'/'][$file->getPath().'/' . $file->getBaseName()] = $file->getBaseName();
		}

		$sortedOutput = $output;
		foreach($sortedOutput as $dir => $content) {
			asort($sortedOutput[$dir]);
		}

		ksort($sortedOutput);

		return $sortedOutput;
	}
}
