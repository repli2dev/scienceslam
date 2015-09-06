<?php

class SignupForm extends VisualControl {
	/** @var \Nette\Mail\IMailer */
	private $mailer;
	/** @var \Muni\ScienceSlam\Model\Event */
	private $event;

	private $from;
	private $to;
	private $mail;

	public function __construct(\Nette\Mail\IMailer $mailer, \Muni\ScienceSlam\Model\Event $event) {
		$this->mailer = $mailer;
		$this->event = $event;
		$current = $this->event->findWithOpenedRegistration();
		if($current !== FALSE) {
			$this->from = $current->registration_opened;
			$this->to = $current->registration_closed;
		}
	}

	public function setMail($mail) {
		$this->mail = $mail;
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
			->addRule($form::EMAIL, 'Zadejte, prosím, svoji e-mailovou adresu ve tvaru nekdo@nekde.koncovka.');
		$form->addText('phone', 'Telefonní číslo:')
			->setRequired('Zadejte, prosím, své telefonní číslo.');
		$form->addText('uco', 'UČO (pokud jste z MU):')
//			->setRequired('Zadejte, prosím, své UČO (univerzitní číslo osoby), pokud jste z Masarykovy univerzity.')
			->addRule(\Nette\Forms\Form::INTEGER, 'UČO musí být číslo');

		$form->addGroup('Téma vystoupení');
		$form->addTextArea('content','Krátce představte téma nebo myšlenku vašeho Science slamu:', 45,5)
			->setRequired('Zadejte, prosím, téma vystoupení.');
		$form->setCurrentGroup();
		$form->addSubmit('send', 'Odeslat přihlášku');
		$form->onSuccess[] = $this->formSucceeded;
		return $form;
	}

	public function formSucceeded($form) {
		if(new \Nette\DateTime() < $this->from || new \Nette\DateTime() > $this->to) {
			$form->addError('Bohužel si momentálně nelze registrovat. Omlouváme se.');
			return;
		}
		$values = $form->getValues();
		$message = new \Nette\Mail\Message();
		foreach($this->mail as $rcpt) {
			$message->addTo($rcpt);
		}
		$message->setFrom($values['email'], $values['name']);
		$message->setSubject('Science slam: přihláška');
		$message->setHtmlBody(
			'<p>Science slam: přihláška řečníka.</p>'
			.'<p>Jméno a příjmení: '.$values['name'].'<br />'
			.'E-mailová adresa: '.$values['email'].'<br />'
			.'Telefonní číslo: '.$values['phone'].'<br />'
			.'UČO: '.$values['uco'].'<br />'
			.'Téma vystoupení:<br /><em>'.nl2br($values['content']).'</em></p>'
			.'<p>S pozdravem<br />Science Slam web</p>'
		);
		try {
			$this->mailer->send($message);
		} catch (Exception $ex) {
			$form->addError('Odeslání přihlášky se nezdařilo. Prosím, kontaktujte nás.');
			return;
		}
		$this->flashMessage('Vaše přihláška byla úspěšně odeslána. Vyčkejte prosím.','success');
		$this->redirect('this');
	}
}
