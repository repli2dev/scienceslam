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

		$router[] = new Route('/admin', 'Admin:');

		$router[] = new Route('/slam/edit', 'Slam:edit');
		$router[] = new Route('/slam/delete', 'Slam:delete');
		$router[] = new Route('/slam/add', 'Slam:add');
		$router[] = new Route('/slam/list', 'Slam:list');
		// /slam/one/<url>
		$router[] = new Route('/slam/<eventUrl>[/<pageUrl>]', 'Page:show');
		// /page/<url>
		$router[] = new Route('/show/<pageUrl>', 'Page:show');

		// Old URLs
		$router[] = new Route('/homepage/<pageUrl>', 'Page:old', Route::ONE_WAY);

		$router[] = new Route('<presenter>/<action>', 'Page:show');
		return $router;
	}

}
