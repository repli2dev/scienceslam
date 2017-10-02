<?php

use Muni\ScienceSlam\Utils\TexyFactory;
use Nette\Application\UI;
use Nette\Utils\Html;

class PagePresenter extends BasePresenter {

	protected $resource = 'page';

	/** @var \Muni\ScienceSlam\Model\Event */
	private $eventDAO;

	/** @var \Muni\ScienceSlam\Model\Page */
	private $pageDAO;

	/** @var \Muni\ScienceSlam\Model\Block */
	private $blockDAO;

	/** @var \Muni\ScienceSlam\Utils\PreviewStorage */
	private $previewStorage;

	/** @var GalleryControlFactory */
	private $galleryControlFactory;

	public function injectEventDAO(\Muni\ScienceSlam\Model\Event $eventDAO) {
		$this->eventDAO = $eventDAO;
	}
	public function injectPageDAO(\Muni\ScienceSlam\Model\Page $pageDAO) {
		$this->pageDAO = $pageDAO;
	}
	public function injectBlockDAO(\Muni\ScienceSlam\Model\Block $blockDAO) {
		$this->blockDAO = $blockDAO;
	}
	public function injectPreviewStorage(\Muni\ScienceSlam\Utils\PreviewStorage $previewStorage) {
		$this->previewStorage = $previewStorage;
	}
	public function injectGalleryControlFactory(IGalleryControlFactory $galleryControlFactory) {
		$this->galleryControlFactory = $galleryControlFactory;
	}

	public function startup() {
		parent::startup();
		if($this->getAction() != 'Show' && $this->getAction() != 'show' && $this->getAction() != 'old') {	// Only show is for public access
			$this->perm();
		}
	}

	public function actionOld($pageUrl) {
		$pages = $this->pageDAO->findByUrl($pageUrl);
		if(count($pages) == 0) {
			throw new \Nette\Application\BadRequestException;
		}
		$page = reset($pages);
		if(!empty($page->event_id)) {
			$event = $this->eventDAO->find($page->event_id);
			if($event === FALSE) {
				$this->redirect('Page:show', null, $page->url);
			} else {
				$this->redirect('Page:show', $event->url, $page->url);
			}
		} else {
			$this->redirect('Page:show', null, $pageUrl);
		}
	}

	public function actionShow($eventUrl, $pageUrl) {
		$event = $this->eventDAO->findByUrl($eventUrl);
		$eventId = null;
		if($event !== FALSE) {
			$eventId = $event->event_id;
		}
		if(empty($pageUrl) && empty($eventId)) {
			$page = $this->pageDAO->findDefaultNotInEvent();
		} else if(empty($pageUrl) && !empty($eventId)) {
			$page = $this->pageDAO->findDefaultInEvent($eventId);
		} else {
			$page = $this->pageDAO->findByUrlAndEventId($pageUrl, $eventId);
		}
		if($page === FALSE) {
			throw new \Nette\Application\BadRequestException;
		}
		if($page->hidden && !($this->user->isInRole('manager') || $this->user->isInRole('admin'))) {
			throw new \Nette\Application\BadRequestException();
		}
		if ($page->is_meta_gallery) {
			$galleries = $this->prepareMetaGallery();
			$this->template->galleries = $galleries;
		}
		$blocks = $this->blockDAO->findByPageId($page->page_id);
		$this->template->page = $page;
		$this->template->event = $event;
		$this->template->blocks = $blocks;
		$this->template->admin = $this->user->isInRole('manager') || $this->user->isInRole('admin');
	}

	public function actionPreview($token)
	{
		if ($this->previewStorage->has($token)) {
			list($page, $block) = $this->previewStorage->get($token);
			$blocks = [];
			if (isset($page->page_id) && $page->page_id) {
				$blocks = $this->blockDAO->findByPageId($page->page_id);
			}
			$event = new stdClass();
			if ($block) {
				$page = $this->pageDAO->find($block->page_id);
				$blocks = $this->blockDAO->findByPageId($page->page_id);
				if (isset($block->block_id) && $block->block_id) {
					$blocks[$block->block_id] = $block;
				} else {
					$blocks[] = $block;
				}
				// Sort to take into updated weight into account
				usort($blocks, function ($a, $b) {
					return $a->weight - $b->weight;
				});
			}
			if (isset($page->event_id) && $page->event_id) {
				$event = $this->eventDAO->find($page->event_id);
			}
			if (isset($page->is_meta_gallery) && $page->is_meta_gallery) {
				$galleries = $this->prepareMetaGallery();
				$this->template->galleries = $galleries;
			}
			$this->template->page = $page;
			$this->template->blocks = $blocks;
			$this->template->event = $event;
			$this->template->admin = false;
			$this->template->isPreview = true;
			$this->setView('show');
		}
	}

	public function actionAdd($eventId) {
		$data = $this->eventDAO->find($eventId);
		if($data !== FALSE) {
			$this->template->event = $data;
		}

	}

	public function actionList($eventId) {
		$event = $this->eventDAO->find($eventId);
		$this->template->event = $event;
		$data = $this->pageDAO->findByEventId($event);
		$this->template->data = $data;
	}

	public function actionEdit($id) {
		$data = $this->pageDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$event = $this->eventDAO->find($data->event_id);
		$this->getComponent('editForm')->setDefaults($data);
		$this->template->data = $data;
		$this->template->event = $event;
	}
	public function actionDelete($id) {
		$data = $this->pageDAO->find($id);
		if($data === FALSE) {
			throw new \Nette\Application\BadRequestException();
		}
		$event = $this->eventDAO->find($data->event_id);
		$this->template->data = $data;
		$this->template->event = $event;
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
		$values['event_id'] = $this->getParameter('eventId');
		$values['inserted'] = new \Nette\DateTime();
		$values['url'] = \Nette\Utils\Strings::webalize($values['url']);
		// Add checks for dates etc.

		$row = $this->pageDAO->create();
		$row->addAll($values);

		if ($form['preview']->isSubmittedBy()) {
			$temp = (object) $row->getCurrentToArray();
			$temp->page_id = null;
			$this->template->previewToken = $this->previewStorage->save($temp);
			return;
		}

		try {
			$this->pageDAO->save($row);
		} catch(PDOException $ex) {
			throw $ex;
			$form->addError('URL identifikace již existuje, zadejte, prosím, jinou.');
			return;
		}
		$this->flashMessage('Stránka byla úspěšně přidána.', 'success');
		$this->redirect('list', $this->getParameter('eventId'));
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
		$values['url'] = \Nette\Utils\Strings::webalize($values['url']);

		$row = $this->pageDAO->find($this->getParameter('id'));
		$row = \JanDrabek\Database\WatchingActiveRow::fromActiveRow($row);
		$row->addAll($values);

		if ($form['preview']->isSubmittedBy()) {
			$temp = (object) $row->getCurrentToArray();
			$this->template->previewToken = $this->previewStorage->save($temp);
			return;
		}

		try {
			$this->pageDAO->save($row);
		} catch(PDOException $ex) {
			$form->addError('URL identifikace již existuje, zadejte, prosím, jinou.');
			return;
		}
		$this->flashMessage('Stránka byla úspěšně upravena.', 'success');
		$event = $this->eventDAO->find($row->event_id);
		if ($event === FALSE) {
			$this->redirect('show', null, $row->url);
		} else {
			$this->redirect('show', $event->url, $row->url);
		}
	}

	protected function createComponentDeleteForm() {
		$form = new UI\Form();
		$form->addSubmit('yes', 'Ano');
		$form->addSubmit('no', 'Ne');
		$form->onSuccess[] = $this->deleteFormSucceeded;
		return $form;
	}

	public function deleteFormSucceeded(\Nette\Forms\Form $form) {
		$page = $this->pageDAO->find($this->getParameter('id'));
		if($form['yes']->isSubmittedBy()) {
			$this->pageDAO->delete($this->getParameter('id'));
			$this->flashMessage('Stránka byla úspěšně smazána.', 'success');
		}
		$this->redirect('list', $page->event_id);
	}

	private function prepareForm() {
		$form = new UI\Form;
		$form->getElementPrototype()->class('wide');
		$form->addGroup('Obecné a servisní');
		$form->addText('name', 'Název')
			->setRequired('Vyplňte, prosím, název.');
		$form->addText('url', 'URL identifikace')
			->setRequired('Vyplňte, prosím, identifikaci slamu pro potřeby URL adresy.')
			->setOption('description', 'nutné pro URL adresu, unikátní');
		$form->addCheckbox('is_default', 'Výchozí stránka slamu');
		$form->addCheckbox('hidden', 'Skryto');

		$form->addGroup('Bloky');
		$form->addCheckbox('is_block_page', 'Použít');
		$form->addGroup('Textová stránka');
		$form->addTextArea('content', '', 53, 15)
			->setOption('description', TexyFactory::getSyntaxHelp('page'))
			->getControlPrototype()->class = 'full-width';
		$form->addGroup('Galerie');
		$description = Html::el()
			->add(Html::el()->setText('Například: '))
			->add(Html::el('span class=line-pre')->setText('/images/2013-0'));
		$form->addText('gallery_path', 'Cesta ke galerii')
			->setOption('description', $description);
		$form->addCheckbox('gallery_meta', 'Zobrazit v meta-galerii');
		$description = Html::el()
			->add(Html::el()->setText('Volitelné, například: '))
			->add(Html::el('span class=line-pre')->setText('/images/2013-0/slam1.jpg'))
			->add((Html::el()->setText(' výchozí je první obrázek.')));
		$form->addText('gallery_meta_title', 'Titulní obrázek')
			->setOption('description', $description);
		$form->addText('gallery_meta_subtitle', 'Podtitulek')
			->setOption('description', 'v meta-galerii pod názvem stránky');
		$form->addText('gallery_meta_weight', 'Váha v meta-galerii')
			->addRule(\Nette\Forms\Form::INTEGER, 'Váha musí být celé číslo.')
			->setDefaultValue(0);
		$form->addGroup('Meta galerie');
		$form->addCheckbox('is_meta_gallery', 'Použít metagalerii');
		$form->setCurrentGroup(null);

		return $form;
	}

	public function handleToggle($blockId)
	{
		$this->blockDAO->toggle($blockId);
		if ($this->isAjax()) {
			$block = $this->blockDAO->find($blockId);
			$this->template->blocks = $this->blockDAO->findByPageId($block->page_id);
			$this->redrawControl('blocks');
		} else {
			$this->redirect('this');
		}
	}

	public function handleUp($blockId)
	{
		$this->blockDAO->moveUp($blockId);
		if ($this->isAjax()) {
			$block = $this->blockDAO->find($blockId);
			$this->template->blocks = $this->blockDAO->findByPageId($block->page_id);
			$this->redrawControl('blocks');
		} else {
			$this->redirect('this');
		}
	}

	public function handleDown($blockId)
	{
		$this->blockDAO->moveDown($blockId);
		if ($this->isAjax()) {
			$block = $this->blockDAO->find($blockId);
			$this->template->blocks = $this->blockDAO->findByPageId($block->page_id);
			$this->redrawControl('blocks');
		} else {
			$this->redirect('this');
		}
	}

	public function handleGalleryUp($pageId)
	{
		$this->pageDAO->moveGalleryUp($pageId);
		if ($this->isAjax()) {
			$this->template->galleries = $this->prepareMetaGallery();
			$this->redrawControl('meta-gallery-blocks');
		} else {
			$this->redirect('this');
		}
	}

	public function handleGalleryDown($pageId)
	{
		$this->pageDAO->moveGalleryDown($pageId);
		if ($this->isAjax()) {
			$this->template->galleries = $this->prepareMetaGallery();
			$this->redrawControl('meta-gallery-blocks');
		} else {
			$this->redirect('this');
		}
	}

	protected function createComponentGallery()
	{
		return $this->galleryControlFactory->create();
	}

	protected function prepareMetaGallery()
	{
		$pages = $this->pageDAO->findGalleries();
		$output = [];
		foreach ($pages as $page) {
			$output[$page->page_id] = $temp = new stdClass();
			$temp->page = $page;
			$temp->event = $page->ref('event');
			$temp->title =  GalleryControl::getGalleryTitleImage($page->gallery_path, $page->gallery_meta_title);
		}
		return $output;
	}
}

