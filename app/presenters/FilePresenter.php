<?php

use Nette\Application\UI;

class FilePresenter extends BasePresenter {

	protected $resource = "file";

	public function startup() {
		parent::startup();
		$this->perm();
	}
	
	public function actionDefault($subpath = null) {
		$params = $this->context->getParameters();
		$subpath = str_replace('..', '', $subpath);
		if(empty($subpath)) {
			$files = array();
			foreach($params['uploads']['dirs'] as $dir) {
				$dir = str_replace('..', '', $dir);
				$files[] = new SplFileInfo(__DIR__ . '/../../' . $dir);
			}
			$path = '';
		} else {
			$path = str_replace('..', '', $subpath);
			$path = __DIR__ . '/../../' .$path;
			$files = iterator_to_array(\Nette\Utils\Finder::find('*')->exclude('.*')->in($path));
		}
		$this->template->files = $files;
		$this->template->subpath = $subpath;
	}

	public function createComponentAddForm($name) {
		$form = new UI\Form();
		$form->addGroup('Vytvořit nový adresář');
		$form->addText('new', 'Jméno adresáře:')
			->setRequired('Vyplňte, prosím, jméno nového adresáře')
			->addRule(UI\Form::MAX_LENGTH,'Délka jména může být maximálně 255 znaků.', 255);
		$form->addSubmit('submitted','Vytvořit');
		$form->onSuccess[] = array($this, 'addFormSubmitted');
		return $form;
	}

	public function addFormSubmitted(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		$subpath = $this->getParameter('subpath');
		$subpath = str_replace('..', '', $subpath);
		if(empty($subpath)) {
			$form->addError('Jméno adresáře nemůže být prázdné');
			return;
		}
		$path = __DIR__ . '/../../' .$subpath . '/' . \Nette\Utils\Strings::webalize($values['new']);
		mkdir($path);
		$this->flashMessage('Nový adresář byl úspěšně vytvořen.', 'success').
		$this->redirect('this');
	}

	public function actionDelete($file) {
		$file = str_replace('..','', $file);
		$path = __DIR__ . '/../../' .$file;

		if(file_exists($path)) {
			if(is_dir($path)) {
				if(count(iterator_to_array(\Nette\Utils\Finder::findFiles('*')->from($path))) > 0) {
					$this->flashMessage('Adresář obsahuje soubory, nelze jej smazat.', 'error');
				} else {
					rmdir($path);
					$this->flashMessage('Položka byla úspěšně smazána.', 'success');
				}
			} else {
				unlink($path);
				$this->flashMessage('Položka byla úspěšně smazána.', 'success');
			}
		} else {
			$this->flashMessage('Položka neexistuje.','error');
		}
		$this->redirect('default', array('subpath' => $this->extractUntilLastSlash($file)));
	}

	public function createComponentUploadForm($name) {
		$form = new UI\Form();
		$form->addGroup('Nahrát nové soubory');
		$form->addUpload('files', NULL, TRUE);
		$form->addSubmit('submitted','Nahrát');
		$form->onSuccess[] = array($this, 'uploadFormSubmitted');
		return $form;
	}
	public function uploadFormSubmitted(\Nette\Forms\Form $form) {
		$values = $form->getValues();
		$subpath = $this->getParameter('subpath');
		$subpath = str_replace('..', '', $subpath);
		if(empty($subpath)) {
			$form->addError('Do tohoto adresáře nelze nahrávat.');
			return;
		}
		$files = $values['files'];
		foreach($files as $file) {
			if($file->isOk()) {
				$path = __DIR__ . '/../../' .$subpath . '/' . $file->getName();
				$file->move($path);
			}
		}
		$this->flashMessage('Upload byl úspěšně dokončen.','success');
		$this->redirect('this');
	}

	public function extractUntilLastSlash($value) {
		return \Nette\Utils\Strings::substring($value, 0, mb_strrpos($value, '/'));
	}
}
