<?php
namespace SPHERE\Application\Platform\Gatekeeper\Authorization\Account;

use SPHERE\Application\IModuleInterface;
use SPHERE\Application\Platform\System\Database\Database;
use SPHERE\Common\Frontend\Icon\Repository\Lock;
use SPHERE\Common\Main;
use SPHERE\Common\Window\Navigation\Link;
use SPHERE\System\Database\Link\Identifier;

/**
 * Class Account
 *
 * @package SPHERE\Application\System\Gatekeeper\Authorization\Account
 */
class Account implements IModuleInterface
{

    public static function registerModule()
    {

        Database::registerService(__CLASS__);

        Main::getDisplay()->addModuleNavigation(
            new Link(new Link\Route(__NAMESPACE__), new Link\Name('Benutzerkonten'), new Link\Icon(new Lock()))
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute(__NAMESPACE__, 'Frontend::frontendAccount')
        );
    }

    /**
     * @return Frontend
     */
    public static function useFrontend()
    {

        return new Frontend();
    }

    /**
     * @return Service
     */
    public static function useService()
    {

        return new Service(new Identifier('Platform', 'Gatekeeper', 'Authorization'),
            __DIR__ . '/Service/Entity', __NAMESPACE__ . '\Service\Entity'
        );
    }

    /**
     * @return Service
     */
    public static function useRead()
    {
        return new Service(new Identifier('Platform', 'Gatekeeper', 'Authorization', 'Slave'),
            __DIR__ . '/Service/Entity', __NAMESPACE__ . '\Service\Entity'
        );
    }
}
