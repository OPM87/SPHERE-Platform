<?php
namespace SPHERE\Application\Api\Platform\Utility;

use SPHERE\Application\Api\ApiTrait;
use SPHERE\Application\Api\Dispatcher;
use SPHERE\Application\IApiInterface;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Account;
use SPHERE\Application\Platform\Utility\Favorite\Favorite as FavoriteApp;
use SPHERE\Application\Platform\Utility\Favorite\Service\Entity\TblFavorite;
use SPHERE\Common\Frontend\Ajax\Emitter\ClientEmitter;
use SPHERE\Common\Frontend\Ajax\Emitter\ServerEmitter;
use SPHERE\Common\Frontend\Ajax\Pipeline;
use SPHERE\Common\Frontend\Ajax\Receiver\AbstractReceiver;
use SPHERE\Common\Frontend\Ajax\Receiver\InlineBlockReceiver;
use SPHERE\Common\Frontend\Ajax\Receiver\MenuDropDownReceiver;
use SPHERE\Common\Frontend\Icon\Repository\FAAngleRight;
use SPHERE\Common\Frontend\Icon\Repository\More;
use SPHERE\Common\Frontend\Icon\Repository\Star;
use SPHERE\Common\Frontend\Icon\Repository\StarEmpty;
use SPHERE\Common\Frontend\Link\Repository\Link;
use SPHERE\Common\Frontend\Link\Repository\Standard;
use SPHERE\System\Extension\Extension;

/**
 * Class Favorite
 * @package SPHERE\Application\Api\Platform\Utility
 */
class Favorite extends Extension implements IApiInterface
{
    use ApiTrait;

    public function getFavorite($Receiver, $Route, $Title, $Description)
    {

        $TblAccount = Account::useRead()->getAccountBySession();

        $NavigationEmitterList = Favorite::pipelineNavigationFavorite( Favorite::receiverNavigation() )->getEmitter();

        if (($Tbl = FavoriteApp::useService()->getFavoriteByRoute( $Route, $TblAccount ))) {
            $Pipeline = Favorite::pipelineRemoveFavorite(Favorite::receiverFavorite()->setIdentifier($Receiver), $Route, $Title, $Description);
            foreach ( $NavigationEmitterList as $Emitter ) {
                $Pipeline->appendEmitter( $Emitter );
            }
            return (new Standard('', Favorite::getEndpoint(), new Star(), array(
                'Route' => $Route,
                'Title' => $Title,
                'Description' => $Description
            ), 'Favorit&nbsp;entfernen'))
                ->ajaxPipelineOnClick( $Pipeline );
        } else {
            $Pipeline = Favorite::pipelineAddFavorite(Favorite::receiverFavorite()->setIdentifier($Receiver), $Route, $Title, $Description);
            foreach ( $NavigationEmitterList as $Emitter ) {
                $Pipeline->appendEmitter( $Emitter );
            }
            return (new Standard('', Favorite::getEndpoint(), new StarEmpty(), array(
                'Route' => $Route,
                'Title' => $Title,
                'Description' => $Description
            ), 'Zu&nbsp;Favoriten'))
                ->ajaxPipelineOnClick( $Pipeline );
        }
    }

    public static function pipelineRemoveFavorite(AbstractReceiver $Receiver, $Route, $Title, $Description)
    {
        $Emitter = new ServerEmitter($Receiver, Favorite::getEndpoint());
        $Emitter->setGetPayload(array(
            Favorite::API_TARGET => 'removeFavorite',
            'Receiver' => $Receiver->getIdentifier()
        ));
        $Emitter->setPostPayload(array(
            'Route' => $Route,
            'Title' => $Title,
            'Description' => $Description
        ));
        $Pipeline = new Pipeline(false);
        $LoadingEmitter = new ClientEmitter($Receiver, (new Standard('', Favorite::getEndpoint(), new More()))->setDisabled());
        $Pipeline->appendEmitter($LoadingEmitter);
        $Pipeline->appendEmitter($Emitter);

        return $Pipeline;
    }

    /**
     * @return InlineBlockReceiver
     */
    public static function receiverFavorite()
    {

        return new InlineBlockReceiver();
    }

    /**
     * @return MenuDropDownReceiver
     */
    public static function receiverNavigation()
    {

        return (new MenuDropDownReceiver( '', '', new Star() ))
            ->setIdentifier(MenuDropDownReceiver::IDENTIFIER_PREFIX.'-'.sha1(__METHOD__));
    }

    public function navigationFavorite()
    {
        $FavoriteList = '';
        if (($TblAccount = Account::useRead()->getAccountBySession())) {
            if(($TblFavoriteAll = FavoriteApp::useService()->getFavoriteAll($TblAccount))) {
                $FavoriteList = array();
                array_walk($TblFavoriteAll, function( TblFavorite $TblFavorite ) use (&$FavoriteList){
                    $FavoriteList[] = new Link( $TblFavorite->getTitle(), $TblFavorite->getRoute(), new FAAngleRight(), array(), $TblFavorite->getDescription() );
                });
                $FavoriteList = '<li class="dropdown-header">Favoriten</li><li role="presentation" class="divider"></li><li>'.implode('</li><li>', $FavoriteList).'</li>';
            }
        }
        if( empty( $FavoriteList ) ) {
            return '<li class="dropdown-header">Keine Favoriten gespeichert</li>';
        }
        return $FavoriteList;
    }

    public static function pipelineNavigationFavorite(AbstractReceiver $Receiver)
    {
        $Emitter = new ServerEmitter($Receiver, Favorite::getEndpoint());
        $Emitter->setGetPayload(array(
            Favorite::API_TARGET => 'navigationFavorite',
            'Receiver' => $Receiver->getIdentifier()
        ));
        $Pipeline = new Pipeline(false);
        $Pipeline->appendEmitter($Emitter);

        return $Pipeline;
    }

    public static function pipelineAddFavorite(AbstractReceiver $Receiver, $Route, $Title, $Description)
    {
        $Emitter = new ServerEmitter($Receiver, Favorite::getEndpoint());
        $Emitter->setGetPayload(array(
            Favorite::API_TARGET => 'addFavorite',
            'Receiver' => $Receiver->getIdentifier()
        ));
        $Emitter->setPostPayload(array(
            'Route' => $Route,
            'Title' => $Title,
            'Description' => $Description
        ));
        $Pipeline = new Pipeline(false);
        $LoadingEmitter = new ClientEmitter($Receiver, (new Standard('', Favorite::getEndpoint(), new More()))->setDisabled());
        $Pipeline->appendEmitter($LoadingEmitter);
        $Pipeline->appendEmitter($Emitter);

        return $Pipeline;
    }

    /**
     * @param string $Receiver
     * @param string $Route
     * @param string $Title
     * @param string $Description
     * @return Pipeline
     */
    public function addFavorite($Receiver, $Route, $Title, $Description)
    {
        if (($TblAccount = Account::useRead()->getAccountBySession())) {
            FavoriteApp::useService()->createFavorite($Route, $Title, $Description, $TblAccount);
        }
        return Favorite::pipelineGetFavorite(Favorite::receiverFavorite()->setIdentifier($Receiver), $Route, $Title, $Description);
    }

    public static function pipelineGetFavorite(AbstractReceiver $Receiver, $Route, $Title, $Description)
    {
        $Emitter = new ServerEmitter($Receiver, Favorite::getEndpoint());
        $Emitter->setGetPayload(array(
            Favorite::API_TARGET => 'getFavorite',
            'Receiver' => $Receiver->getIdentifier(),
        ));
        $Emitter->setPostPayload(array(
            'Route' => $Route,
            'Title' => $Title,
            'Description' => $Description
        ));
        $Pipeline = new Pipeline(false);
        $LoadingEmitter = new ClientEmitter($Receiver, (new Standard('', Favorite::getEndpoint(), new More()))->setDisabled());
        $Pipeline->appendEmitter($LoadingEmitter);
        $Pipeline->appendEmitter($Emitter);

        return $Pipeline;
    }

    public function removeFavorite($Receiver, $Route, $Title, $Description)
    {
        if (($TblAccount = Account::useRead()->getAccountBySession())) {
            FavoriteApp::useService()->destroyFavorite($Route, $TblAccount);
        }
        return Favorite::pipelineGetFavorite(Favorite::receiverFavorite()->setIdentifier($Receiver), $Route, $Title, $Description);
    }

    /**
     * @param string $Method
     * @return string
     */
    public function exportApi($Method = '')
    {
        $Dispatcher = new Dispatcher(__CLASS__);

        $Dispatcher->registerMethod('getFavorite');
        $Dispatcher->registerMethod('addFavorite');
        $Dispatcher->registerMethod('removeFavorite');
        $Dispatcher->registerMethod('navigationFavorite');

        return $Dispatcher->callMethod($Method);
    }

}
