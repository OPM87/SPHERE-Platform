<?php
/**
 * Created by PhpStorm.
 * User: schubert
 * Date: 25.10.2016
 * Time: 13:19
 */

namespace SPHERE\Application\RROP\OtherApplication;


use SPHERE\Application\IApplicationInterface;
//use SPHERE\Application\Reporting\Controlling\DirectSearch\DirectSearch;
use SPHERE\Common\Frontend\Icon\Repository\Search as SearchIcon;
use SPHERE\Common\Main;
use SPHERE\Common\Window\Navigation\Link;
use SPHERE\Common\Window\Stage;

class OtherApplication implements IApplicationInterface
{
	public static function registerApplication()
	{
		Main::getDisplay()->addApplicationNavigation(
            new Link(new Link\Route(__NAMESPACE__), new Link\Name('Weitere Anwendungen'), new Link\Icon(new SearchIcon()))
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute(__NAMESPACE__, __CLASS__ . '::frontendDashboard')
        );
//		Search::registerModule();
//		DirectSearch::registerModule();
//		MonthlyTurnover::registerModule();
	}

	/**
	  * @return Stage
	  */
	 public function frontendDashboard()
	 {
	     $Stage = new Stage('Weitere Anwendungen');
	     $Stage->setMessage('');
	     return $Stage;
	 }

}