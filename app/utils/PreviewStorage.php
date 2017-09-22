<?php
namespace Muni\ScienceSlam\Utils;

use DateTime;
use Nette\Http\Session;
use Nette\Utils\Random;

class PreviewStorage
{
    const PAGE = 'page';
    const BLOCK = 'block';

    /** @var $session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function has($token)
    {
        return $this->session->hasSection(static::class . '/' . $token);
    }

    public function get($token)
    {
        if ($this->session->hasSection(static::class . '/' . $token)) {
            $section = $this->session->getSection(static::class . '/' . $token);
            return [$section->offsetGet(static::PAGE), $section->offsetGet(static::BLOCK)];
        }
        return null;
    }

    public function save($page, $block = null)
    {
        $token = Random::generate();
        $section = $this->session->getSection(static::class . '/' . $token);
        $section->setExpiration(new DateTime('+20 minutes'));
        $section->offsetSet(static::PAGE, $page);
        $section->offsetSet(static::BLOCK, $block);
        return $token;
    }
}
