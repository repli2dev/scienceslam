<?php
namespace Muni\ScienceSlam\Utils;

use Latte\Engine;
use Nette\Utils\Html;
use Texy\Bridges\Latte\TexyMacro;
use Texy\Texy;

class TexyFactory
{
	/** @var Texy */
	private $texy;

	public static function getSyntaxHelp($id = null)
	{
		$wrapper = Html::el();
		$button = Html::el('a')->setText('Nápověda formátování');
		$button->class = 'help-button';
		$button->onclick = 'w3.toggleShow(\'#formatting-help-' . $id . '\'); return false;';
		$wrapper->add($button);
		$el = Html::el('div')->add(
			Html::el('pre')->setText('
Nadpis první úrovně (nižší úrovně # * = -)
##########################################

Odstavce se dělají oddělením pomocí prázdného řádku.

------

/---div .[wrapper]

Nečíslovaný seznam se dělá takto:
.[spaced]
- **tučný** řez písmo nebo *kurzíva*
- a takto se dělá "odkaz":http://scienceslam.cz/show/about
- u číslovaného seznamu nahraďte - za 1)

\---

[* /images/blocks/preview01-spectrometer.jpg .(alternativní text)[image_right clear] *]')
		);
		$el->add(Html::el()->setText('Dostupné formátovací třídy:'));
		$el->add(Html::el('pre')->setText('div .[wide-text-block]'));
		$el->add(Html::el('pre')->setText('div .[wrapped]'));
		$el->add(Html::el('pre')->setText('.[spaced]'));
		$el->add(Html::el('br'));
		$el->add(
			Html::el('a')->href('https://texy.info/cs/syntax')->setHtml('<br>')->setText('Detailní dokumentace syntaxe')
		);
		$el->id = 'formatting-help-' . $id;
		$el->class = 'help-block';
		$el->style = 'display: none';
		$wrapper->add($el);
		return $wrapper;
	}

	public function getTexy()
	{
		if (!$this->texy) {
			$this->texy = new Texy();
		}
		return $this->texy;
	}

	public function install(Engine $engine)
	{
		$temp = new TexyMacro($engine, $this->getTexy());
		$temp->install();
		$engine->addFilter('texy', [$this->getTexy(), 'process']);
	}
}