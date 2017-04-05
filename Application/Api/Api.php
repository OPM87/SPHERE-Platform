<?php
namespace SPHERE\Application\Api;

use SPHERE\Application\Api\Platform\Platform;
use SPHERE\Application\Api\Reporting\Reporting;
use SPHERE\Application\Api\TestAjax\TestAjax;
use SPHERE\Application\IClusterInterface;

/**
 * Class Api
 *
 * @package SPHERE\Application\Api
 */
class Api implements IClusterInterface
{

    public static function registerCluster()
    {
        Platform::registerApplication();
        Reporting::registerApplication();
    }
}
