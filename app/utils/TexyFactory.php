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

	public static function getSyntaxHelp()
	{
		$el = Html::el('div')->add(
			Html::el('pre')->setText('
## Nadpis první úrovně

Odstavce se dělají oddělením pomocí prázdného řádku.

------

/---div .[wrapper]

Nečíslovaný seznam se dělá takto:
- **tučný** řez písmo nebo *kurzíva*
- a takto se dělá "odkaz":http://scienceslam.cz/show/about
- u číslovaného seznamu nahraďte - za 1)

\---

[* /images/blocks/preview01-spectrometer.jpg .(alternativní text)[image_right clear] *]')
		);
		$el->add(
			Html::el('a')->href('https://texy.info/cs/syntax')->setHtml('<br>')->setText('Dokumentace syntaxe')
		);
		return $el;
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