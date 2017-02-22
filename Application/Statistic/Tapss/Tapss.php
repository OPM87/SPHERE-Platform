<?php
/**
 * Created by PhpStorm.
 * User: Kunze
 * Date: 22.02.2017
 * Time: 00:41
 */

namespace SPHERE\Application\Statistic\Tapss;


use SPHERE\Application\IApplicationInterface;
use SPHERE\Application\Statistic\Tapss\Eigene\Eigene;
use SPHERE\Application\Statistic\Tapss\Individual\Individual;
use SPHERE\Application\Statistic\Tapss\Standard\Standard;
use SPHERE\Common\Frontend\Icon\Repository\Wrench;
use SPHERE\Common\Main;
use SPHERE\Common\Window\Navigation\Link;

class Tapss implements IApplicationInterface
{
    public static function registerApplication()
    {
        Main::getDisplay()->addApplicationNavigation(
            new Link(new Link\Route(__NAMESPACE__), new Link\Name('TAPSS'), new Link\Icon(new Wrench()))
        );
        Main::getDispatcher()->registerRoute(Main::getDispatcher()->createRoute(
            __NAMESPACE__, __CLASS__ . '::frontendDashboard'
        ));

        Standard::registerModule();
        Individual::registerModule();
        Eigene::registerModule();
    }

}