<?php

use Nette\Application\Responses\FileResponse;
use Nette\Application\UI;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\InvalidStateException;
use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;

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
			$this->template->enterable = is_executable($path);
			$files = iterator_to_array(Finder::find('*')->exclude('.*')->in($path));
		}
		ksort($files);
		$this->template->files = $files;
		$this->template->subpath = $subpath;
	}

	public function actionDownLoad($path, $originalMimeType = false) {
		$file = __DIR__ . '/../../' . $path;
		if ($originalMimeType && is_file($file)) {
			$contentType = mime_content_type($file);
			$forceDownload = false;
		}
		$response = new FileResponse($file, null, isset($contentType) ? $contentType : null, isset($forceDownload) ? $forceDownload : null);
		$this->sendResponse($response);
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

	public function addFormSubmitted(Form $form) {
		$values = $form->getValues();
		$subpath = $this->getParameter('subpath');
		$subpath = str_replace('..', '', $subpath);
		if(empty($subpath)) {
			$form->addError('Jméno adresáře nemůže být prázdné');
			return;
		}
		$path = __DIR__ . '/../../' .$subpath . '/' . \Nette\Utils\Strings::webalize($values['new']);
		if (!@mkdir($path) && !is_dir($path)) {
			$form->addError('Nový adresář se nepodařilo kvůli oprávnění vytvořit.');
			return;
		}
		$this->flashMessage('Nový adresář byl úspěšně vytvořen.', 'success').
		$this->redirect('this');
	}

	public function actionDelete($file) {
		$file = str_replace('..','', $file);
		$path = __DIR__ . '/../../' .$file;

		if(file_exists($path)) {
			if(is_dir($path)) {
				if(count(iterator_to_array(Finder::findFiles('*')->from($path))) > 0) {
					$this->flashMessage('Adresář obsahuje soubory, nelze jej smazat.', 'error');
				} else {
					try {
						FileSystem::delete($path);
						$this->flashMessage('Adresář byl úspěšně smazán.', 'success');
					} catch (IOException $exception) {
						$this->flashMessage('Adresář se nepodařilo smazat z důvodu špatných oprávnění.', 'error');
					}
				}
			} else {
				try {
					FileSystem::delete($path);
					$this->flashMessage('Položka byla úspěšně smazána.', 'success');
				} catch (IOException $exception) {
					$this->flashMessage('Položka se z důvodu špatných oprávnění adresáře nepodařilo smazat.', 'error');
				}
			}
		} else {
			$this->flashMessage('Položka neexistuje.','error');
		}
		$this->redirect('default', array('subpath' => $this->extractUntilLastSlash($file)));
	}

	public function fixPathToUploadBase($filePath) {
		return str_replace(__DIR__ . '/../..//', '', $filePath);
	}

	public function createComponentMultiDeleteForm($name)
	{
		$form = new UI\Form();
		$checkboxes = [];
		foreach ((array) $this->template->files as $filePath => $file) {
			$checkboxes[$this->fixPathToUploadBase($filePath)] = basename($filePath);
		}
		$form->addCheckboxList('files', 'Soubory', $checkboxes)
			->setRequired('Vyberte alespoň jeden soubor ke smazání');
		$form->addSubmit('submitted','Smazat vybrané');
		$form->onSuccess[] = array($this, 'multiDeleteFormSubmitted');
		return $form;
	}

	public function multiDeleteFormSubmitted(Form $form) {
		$values = $form->getValues();
		$error = false;
		foreach ($values->files as $file) {
			$file = str_replace('..','', $file);
			$path = __DIR__ . '/../../' .$file;

			try {
				FileSystem::delete($path);
			} catch (IOException $exception) {
				$this->flashMessage(sprintf("Položku [%s] se z důvodu špatných oprávnění nepodařilo smazat.", $path), 'error');
				$error = true;
			}
		}
		$this->flashMessage($error ? 'Některé položky se nepodařilo smazat.' : 'Položky byla úspěšně smazány.', $error ? 'error' : 'success');
		$this->redirect('this');
	}

	public function createComponentUploadForm($name) {
		$form = new UI\Form();
		$form->addGroup('Nahrát nové soubory');
		$form->addUpload('files', NULL, TRUE);
		$form->addCheckbox('overwrite', 'Přepsat existuje-li');
		$form->addSubmit('submitted','Nahrát');
		$form->onSuccess[] = array($this, 'uploadFormSubmitted');
		return $form;
	}

	public function uploadFormSubmitted(Form $form) {
		$values = $form->getValues();
		$subpath = $this->getParameter('subpath');
		$subpath = str_replace('..', '', $subpath);
		if(empty($subpath)) {
			$form->addError('Do tohoto adresáře nelze nahrávat.');
			return;
		}
		$files = $values['files'];
		/** @var FileUpload $file */
		foreach($files as $file) {
			if($file->isOk()) {
				$path = __DIR__ . '/../../' .$subpath . '/' . $file->getName();
				if (is_dir($path)) {
					$form->addError(sprintf('Upload souboru [%s] selhal, nelze přepsat adresář.', $file->getName()));
					return;
				}
				if (file_exists($path) && !$values->overwrite) {
					$form->addError(sprintf('Upload souboru [%s] selhal, tento soubor již existuje a přepsání nebylo potvrzeno.', $file->getName()));
					return;
				}
				try {
					$file->move($path);
				} catch (InvalidStateException $exception) {
					$form->addError(sprintf("Upload souboru [%s] selhal kvůli oprávnění a nahrávání bylo zastaveno.", $file->getName()));
					return;
				}
			}
		}
		$this->flashMessage('Upload byl úspěšně dokončen.','success');
		$this->redirect('this');
	}

	public function createComponentFixPermissionsForm($name) {
		$form = new UI\Form();
		$form->addGroup('Opravit oprávnění');
		$form->addCheckbox('confirm', 'Potvrdit')
			->setRequired(true);
		$form->addSubmit('submitted','Opravit oprávnění');
		$form->onSuccess[] = array($this, 'fixPermissionFormSubmitted');
		return $form;
	}
	public function fixPermissionFormSubmitted(Form $form) {
		$params = $this->context->getParameters();
		$error = false;
		foreach($params['uploads']['dirs'] as $dir) {
			$temp = new SplFileInfo(__DIR__ . '/../../' . $dir);
			// Fix all directories
			foreach (Finder::findDirectories('*')->from($temp) as $directory) {
				if (!@chmod($directory, 0777)) {
					$error = true;
					$this->flashMessage(sprintf('Nastavení oprávnění 0666 na adresáři [%s] selhalo. Bude potřeba vyřešit se správcem.', $directory));
				}
			}
			// Fix files
			foreach (Finder::findFiles('*')->from($temp) as $file) {
				if (!@chmod($file, 0666)) {
					$error = true;
					$this->flashMessage(sprintf('Nastavení oprávnění 0666 na souboru [%s] selhalo. Bude potřeba vyřešit se správcem.', $file));
				}
			}
		}
		$this->flashMessage($error ?'Některá oprávnění se nepodařilo opravit.' : 'Oprávnění byla opravena.', $error ? 'error' : 'success');
		$this->redirect('this');
	}

	public function extractUntilLastSlash($value) {
		return \Nette\Utils\Strings::substring($value, 0, mb_strrpos($value, '/'));
	}
}
