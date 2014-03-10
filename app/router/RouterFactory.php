<?php

use Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();		
		$router[] = new Route('index.php', 'Page:show', Route::ONE_WAY);
		// /slam/one/<url>
		$router[] = new Route('/slam/<eventUrl>[/<pageUrl>]', 'Page:Show');
		// /page/<url>
		$router[] = new Route('/show/<pageUrl>', 'Page:Show');
		$router[] = new Route('<presenter>/<action>', 'Page:show');
		return $router;
	}

}
