<?php

use Nette\Forms\Form;

class TicketForm extends VisualControl {
	private $from;
	private $to;
	private $mail;
	/** @var \Nette\Mail\Message */
	private $messagePrototype;

	public function setFrom($from) {
		$this->from = Nette\DateTime::from($from);
	}
	public function setTo($to) {
		$this->to = Nette\DateTime::from($to);
	}
	public function setMail($mail) {
		$this->mail = $mail;
	}
	public function injectMessagePrototype(\Nette\Mail\Message $message) {
		$this->messagePrototype = $message;
	}

	public function beforeRender() {
		parent::beforeRender();
		$this->template->from = $this->from;
		$this->template->to = $this->to;
	}

	protected function createComponentForm() {
		$form = new ResponsiveForm();
		$form->addGroup('Osobní údaje');
		$form->addText('name', 'Jméno a příjmení:')
			->setRequired('Zadejte, prosím, své celé jméno.');

		$form->addText('email', 'E-mailová adresa:')
			->setRequired('Zadejte, prosím, svoji e-mailovou adresu.')
			->addRule(Form::EMAIL, 'Zadejte, prosím, svoji e-mailovou adresu ve tvaru nekdo@nekde.koncovka.');

		$form->addGroup('Počty vstupenek');
		$s = $form->addText('students','Studentů', 4)
			->setOption('description', '70 Kč/lístek')
			->setDefaultValue(1)
			->addCondition(Form::FILLED)
				->addRule(Form::INTEGER, 'Počet objednávaných vstupenek musí být číslo.')
				->addRule(Form::RANGE, 'Počet objednávaných vstupenek musí být mezi 0 a 10.', array(0,10));

		$a = $form->addText('adults','Dospělých', 4)
			->setOption('description', '100 Kč/lístek')
			->setDefaultValue(0)
			->addCondition(Form::FILLED)
				->addRule(Form::INTEGER, 'Počet objednávaných vstupenek musí být číslo.')
				->addRule(Form::RANGE, 'Počet objednávaných vstupenek musí být mezi 0 a 10.', array(0,10));

		$form->addRadioList('handover', 'Vyzvednutí', array('Před akcí', 'Na rektorátu MU'))
			->setRequired('Zvolte, prosím, způsob vyzvednutí.');
		$form->setCurrentGroup();
		$form->addSubmit('send', 'Odeslat objednávku');
		$form->onSuccess[] = $this->formSucceeded;
		return $form;
	}

	public function formSucceeded($form) {
		if(new \Nette\DateTime() < $this->from || new \Nette\DateTime() > $this->to) {
			$form->addError('Bohužel si momentálně nelze objednat lístky. Omlouváme se.');
			return;
		}
		$values = $form->getValues();
		if($values['students'] + $values['adults'] <= 0) {
			$form->addError('Zadejte správný počet vstupenek');
			return;
		}

		$message = clone $this->messagePrototype;
		$message->addTo($this->mail); // poslat oběma stranám
		$message->setFrom($values['email'], $values['name']);
		$message->setSubject('Science slam: objednávka lístků');
		$message->setHtmlBody(
			'<p>Science slam: objednávka lístků.<br />Student: '.$values['students'].'<br />Dospělý: '.$values['adults'].'</p><p>S pozdravem<br />Science Slam web</p>'
		);
		try {
			$message->send();
		} catch (Exception $ex) {
			$form->addError('Odeslání objednávky se nezdařilo. Prosím, kontaktujte nás.');
			return;
		}
		$this->flashMessage('Vaše objednávka byla úspěšně odeslána. Vyčkejte prosím.');
		$this->redirect('this');
	}
}
