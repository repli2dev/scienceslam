<?php

use Muni\ScienceSlam\Model\ListBlockType;
use Muni\ScienceSlam\Utils\TexyFactory;
use Nette\Application\UI;
use Nette\Utils\Html;

class BlockPresenter extends BasePresenter {

	protected $resource = 'block';

	/** @var \Muni\ScienceSlam\Model\Block */
	private $blockDAO;
	/** @var \Muni\ScienceSlam\Model\Page */
	private $pageDAO;
	/** @var \Muni\ScienceSlam\Model\Event */
	private $eventDAO;
	/** @var \Muni\ScienceSlam\Utils\PreviewStorage */
	private $previewStorage;

	public function injectBlockDAO(\Muni\ScienceSlam\Model\Block $blockDAO) {
		$this->blockDAO = $blockDAO;
	}
	public function injectPageDAO(\Muni\ScienceSlam\Model\Page $pageDAO) {
		$this->pageDAO = $pageDAO;
	}
	public function injectEventDAO(\Muni\ScienceSlam\Model\Event $eventDAO) {
		$this->eventDAO = $eventDAO;
	}
	public function injectPreviewStorage(\Muni\ScienceSlam\Utils\PreviewStorage $previewStorage) {
		$this->previewStorage = $previewStorage;
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
		// Check if style has non-existing image
		$allowedFiles = $this->getImages();
		$values = $form->getValues();
		$styleFromSaved = $use['style'];
		$styleFromForm = $values[ListBlockType::IMAGE]['style'];
		$allowedFiles = array_reduce($allowedFiles, 'array_merge', []);
		$use['layout'] = $use['block_type_id'];
		if (((int) ($values['layout'] ?: $use['layout'])) === ListBlockType::IMAGE && !array_key_exists($styleFromForm, $allowedFiles) && !array_key_exists($styleFromSaved, $allowedFiles)) {
			$form->addError("Dosud uložený obrázek [{$use['style']}] (již) neexistuje. Vyberte prosím nový.");
			$use['style'] = null;
		}
		if($use['layout'] == ListBlockType::TEXT) {
			$use[ListBlockType::TEXT]['content'] = $use['param1'];
		}
		if($use['layout'] == ListBlockType::VERTICAL_TEXT) {
			$use[ListBlockType::VERTICAL_TEXT]['content'] = $use['param1'];
		}
		if($use['layout'] == ListBlockType::IMAGE) {
			$use[ListBlockType::IMAGE]['label'] = $use['param1'];
			$use[ListBlockType::IMAGE]['label2'] = $use['param2'];
			$use[ListBlockType::IMAGE]['style'] = $use['style'];
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
		$form->addSubmit('preview', 'Náhled')->getControlPrototype()->class = 'button button-gray';
		$form->addSubmit('send', 'Přidat');
		$form->onSuccess[] = $this->addFormSucceeded;
		return $form;
	}

	public function addFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();

		$output = array();
		$output['block_type_id'] = $layout = $values['layout'];
		if($layout == ListBlockType::TEXT) {
			$output['param1'] = $values[ListBlockType::TEXT]['content'];
		}
		if($layout == ListBlockType::VERTICAL_TEXT) {
			$output['param1'] = $values[ListBlockType::VERTICAL_TEXT]['content'];
		}
		if($layout == ListBlockType::IMAGE) {
			$output['param1'] = $values[ListBlockType::IMAGE]['label'];
			$output['param2'] = $values[ListBlockType::IMAGE]['label2'];
			$output['style'] = $values[ListBlockType::IMAGE]['style'];
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

		// Stop when preview
		if ($form['preview']->isSubmittedBy()) {
			$temp = (object) $row->getCurrentToArray();
			$temp->block_id = null;
			$this->template->previewToken = $this->previewStorage->save(null, $temp);
			return;
		}

		// Continue when saving

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
		$form->addSubmit('preview', 'Náhled')->getControlPrototype()->class = 'button button-gray';
		$form->addSubmit('send', 'Uložit');
		$form->onSuccess[] = $this->editFormSucceeded;
		return $form;
	}

	public function editFormSucceeded(\Nette\Forms\Form $form) {
		$values = $form->getValues();

		$output = array();
		$output['block_type_id'] = $layout = $values['layout'];
		if($layout == ListBlockType::TEXT) {
			$output['param1'] = $values[ListBlockType::TEXT]['content'];
		}
		if($layout == ListBlockType::VERTICAL_TEXT) {
			$output['param1'] = $values[ListBlockType::VERTICAL_TEXT]['content'];
		}
		if($layout == ListBlockType::IMAGE) {
			$output['param1'] = $values[ListBlockType::IMAGE]['label'];
			$output['param2'] = $values[ListBlockType::IMAGE]['label2'];
			$output['style'] = $values[ListBlockType::IMAGE]['style'];
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

		// Stop when preview
		if ($form['preview']->isSubmittedBy()) {
			$temp = (object) $row->getCurrentToArray();
			$this->template->previewToken = $this->previewStorage->save(null, $temp);
			return;
		}

		// Continue when saving
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
		$form->addSelect('layout', 'Rozložení', ListBlockType::getAll())
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

		$form->addGroup(Html::el('span')->setText('Rozložení: Vertikálně centrovaný text')->addAttributes(['class' => 'layout-' . ListBlockType::VERTICAL_TEXT]));
		$c = $form->addContainer(ListBlockType::VERTICAL_TEXT);

		$c->addTextArea('content', 'Obsah', 48, 10)
			->setOption('description', TexyFactory::getSyntaxHelp('vertical'))
			->getControlPrototype()->class = 'full-width';

		$form->addGroup(Html::el('span')->setText('Rozložení: Text')->addAttributes(['class' => 'layout-' . ListBlockType::TEXT]));
		$c = $form->addContainer(ListBlockType::TEXT);
		$c->addTextArea('content', 'Obsah', 48, 10)
			->setOption('description', TexyFactory::getSyntaxHelp('horizontal'))
			->getControlPrototype()->class = 'full-width';

		$form->addGroup(Html::el('span')->setText('Rozložení: Obrázek')->addAttributes(['class' => 'layout-' . ListBlockType::IMAGE]));
		$c = $form->addContainer(ListBlockType::IMAGE);
		$c->addText('label', 'Nadpisek');
		$c->addText('label2', 'Krátký popis');
		$style = $c->addSelect('style', 'Obrázek na pozadí', $this->getImages(), 10)
			->setPrompt('--- Vyberte ---');
		$previewLink = Html::el('a')->href('#')->class('open-select-preview')->setText('Náhled');
		$previewLink->addAttributes(['data-select-id' => $style->getHtmlId()]);

		$style
			->setOption('description', $previewLink);

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
