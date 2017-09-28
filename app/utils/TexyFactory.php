<?php
namespace Muni\ScienceSlam\Utils;

use Latte\Engine;
use Muni\ScienceSlam\Model\Snippet;
use Nette\Utils\Html;
use Texy\BlockParser;
use Texy\Bridges\Latte\TexyMacro;
use Texy\HtmlElement;
use Texy\Texy;

class TexyFactory
{
	/** @var Texy */
	private $texy;

	/** @var Texy */
	private $texyWithoutSnippets;

    /** @var Snippet */
    private $snippetDAO;

    public function __construct(Snippet $snippetDAO)
    {
        $this->snippetDAO = $snippetDAO;
    }

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
 Prosté odřádkování pomocí jedné mezery na začátku řádku.

------

Ve stránkách lze vkládat vytvořené snippety pomocí následující syntaxe: 

.snippet
klic-snipetu

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
        $el->add(Html::el('pre')->setText('.[no-margin]'));
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
			// Add support for including snippets
            $this->texy->registerBlockPattern(
                function (BlockParser $parser, array $matches, $name)
                {
                    list(, $mKey) = $matches;
                    $texy = $this->getTexyWithoutSnippets();

                    $el = new HtmlElement('div');
                    $el->attrs['class'] = 'texy-snippet-' . $mKey;
                    $snippet = $this->snippetDAO->getByKey($mKey);
                    if ($snippet) {
                        $temp = $texy->process($snippet->content);
                        $el->parseBlock($parser->getTexy(), "/---html\n" . $temp . "\n\----\n");
                    }
                    return $el;
                },
                '#^\.snippet\n(.+)$#m', // block patterns must be multiline and line-anchored
                'snippetSyntax'
            );
		}
		return $this->texy;
	}

	public function getTexyWithoutSnippets()
    {
        if (!$this->texyWithoutSnippets) {
            $this->texyWithoutSnippets = new Texy();
            // Eat all .snippets to be eaten
            $this->texyWithoutSnippets->registerBlockPattern(
                function (BlockParser $parser, array $matches, $name)
                {
                    return null;
                },
                '#^\.snippet\n(.+)$#m', // block patterns must be multiline and line-anchored
                'snippetSyntax'
            );
        }
        return $this->texyWithoutSnippets;
    }

	public function install(Engine $engine)
	{
		$temp = new TexyMacro($engine, $this->getTexy());
		$temp->install();
		$engine->addFilter('texy', [$this->getTexy(), 'process']);
        $engine->addFilter('texyWithoutSnippets', [$this->getTexyWithoutSnippets(), 'process']);
	}
}
