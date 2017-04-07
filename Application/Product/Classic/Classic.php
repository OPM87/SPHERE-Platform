<?php
/**
 * Created by PhpStorm.
 * User: schubert
 * Date: 25.10.2016
 * Time: 13:33
 */

namespace SPHERE\Application\Product\Classic;


use SPHERE\Application\IApplicationInterface;
use SPHERE\Application\IServiceInterface;
use SPHERE\Common\Frontend\Icon\Repository\Car;
use SPHERE\Common\Main;
use SPHERE\Common\Window\Navigation\Link;

class Classic implements IApplicationInterface
{
	public static function registerApplication()
	{
		Main::getDisplay()->addApplicationNavigation(
            new Link(new Link\Route(__NAMESPACE__), new Link\Name('Classic'), new Link\Icon(new Car()))
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute(__NAMESPACE__, __NAMESPACE__ . '\Frontend::frontendClassic')
        );
	}

	/**
	 * @return IServiceInterface
	 */
	public static function useService()
	{
		// TODO: Implement useService() method.
	}

	/**
	 * @return Frontend
	 */
	public static function useFrontend()
	{
		return new Frontend();
	}

}