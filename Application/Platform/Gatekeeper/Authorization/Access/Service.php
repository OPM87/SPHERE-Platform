<?php
namespace SPHERE\Application\Platform\Gatekeeper\Authorization\Access;

use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Data;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Entity\TblLevel;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Entity\TblLevelPrivilege;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Entity\TblPrivilege;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Entity\TblPrivilegeRight;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Entity\TblRight;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Entity\TblRole;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Entity\TblRoleLevel;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Setup;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Account;
use SPHERE\Common\Frontend\Form\IFormInterface;
use SPHERE\Common\Window\Redirect;
use SPHERE\System\Cache\Handler\MemcachedHandler;
use SPHERE\System\Database\Binding\AbstractService;
use SPHERE\System\Database\Fitting\Element;

/**
 * Class Service
 *
 * @package SPHERE\Application\System\Gatekeeper\Authorization\Access
 */
class Service extends AbstractService
{

    /** @var array $AuthorizationRequest */
    private static $AuthorizationRequest = array();
    /** @var array $AuthorizationCache */
    private static $AuthorizationCache = array();

    /**
     * @param bool $doSimulation
     * @param bool $withData
     *
     * @return string
     */
    public function setupService($doSimulation, $withData)
    {

        $Protocol = (new Setup($this->getStructure()))->setupDatabaseSchema($doSimulation);
        if (!$doSimulation && $withData) {
            (new Data($this->getBinding()))->setupDatabaseContent();
        }
        return $Protocol;
    }

    /**
     * @param string $Route
     *
     * @return bool
     */
    public function hasAuthorization($Route)
    {

        // Sanitize Route
        $Route = '/' . trim($Route, '/');

        // Cache
        $this->hydrateAuthorization();
        if (in_array($Route, self::$AuthorizationCache) || in_array($Route, self::$AuthorizationRequest)) {
            return true;
        }
        if ($this->existsRightByName($Route) || preg_match('!^/Api/!is', $Route)) {
            // MUST BE protected -> Access denied
            return false;
        } else {
            // Access valid PUBLIC -> Access granted
            self::$AuthorizationRequest[] = $Route;
            return true;
        }
    }

    private function hydrateAuthorization()
    {

        if (empty(self::$AuthorizationCache)) {
            if (($TblAccount = Account::useRead()->getAccountBySession())) {
                $Cache = $this->getCache(new MemcachedHandler());
                if (!($AuthorizationCache = $Cache->getValue($TblAccount->getId(), __METHOD__))) {
                    if (($TblAuthorizationAll = Account::useService()->getAuthorizationAllByAccount($TblAccount))) {
                        /** @var \SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Service\Entity\TblAuthorization $TblAuthorization */
                        foreach ($TblAuthorizationAll as $TblAuthorization) {
                            $TblRole = $TblAuthorization->getServiceTblRole();
                            if ($TblRole && (false !== ($TblLevelAll = $TblRole->getTblLevelAll()))) {
                                /** @var TblLevel $TblLevel */
                                foreach ($TblLevelAll as $TblLevel) {
                                    $TblPrivilegeAll = $TblLevel->getTblPrivilegeAll();
                                    if ($TblPrivilegeAll) {
                                        /** @var TblPrivilege $TblPrivilege */
                                        foreach ($TblPrivilegeAll as $TblPrivilege) {
                                            $TblRightAll = $TblPrivilege->getTblRightAll();
                                            if ($TblRightAll) {
                                                /** @var TblRight $TblRight */
                                                foreach ($TblRightAll as $TblRight) {
                                                    if (!in_array($TblRight->getRoute(), self::$AuthorizationCache)) {
                                                        array_push(self::$AuthorizationCache, $TblRight->getRoute());
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $Cache->setValue($TblAccount->getId(), self::$AuthorizationCache, 0, __METHOD__);
                } else {
                    self::$AuthorizationCache = $AuthorizationCache;
                }
            }
        }
    }

    /**
     * @param string $Name
     *
     * @return bool
     */
    public function existsRightByName($Name)
    {

        return (new Data($this->getBinding()))->existsRightByName($Name);
    }

    /**
     * @param string $Name
     *
     * @return null|TblRight
     */
    public function getRightByName($Name)
    {

        return (new Data($this->getBinding()))->getRightByName($Name);
    }

    /**
     * @param integer $Id
     *
     * @return null|TblRight
     */
    public function getRightById($Id)
    {

        return (new Data($this->getBinding()))->getRightById($Id);
    }

    /**
     * @return null|TblRight[]
     */
    public function getRightAll()
    {

        return (new Data($this->getBinding()))->getRightAll();
    }

    /**
     * @param IFormInterface $Form
     * @param null|string $Name
     *
     * @return IFormInterface|Redirect
     */
    public function createRight(IFormInterface $Form, $Name)
    {

        if (null !== $Name && empty($Name)) {
            $Form->setError('Name', 'Bitte geben Sie einen Namen ein');
        }
        if (!empty($Name)) {
            $Form->setSuccess('Name', 'Das Recht wurde hinzugefügt');
            (new Data($this->getBinding()))->createRight($Name);
            return new Redirect('/Platform/Gatekeeper/Authorization/Access/Right', 0);
        }
        return $Form;
    }

    /**
     * @param IFormInterface $Form
     * @param null|string $Name
     *
     * @return IFormInterface|Redirect
     */
    public function createPrivilege(IFormInterface $Form, $Name)
    {

        if (null !== $Name && empty($Name)) {
            $Form->setError('Name', 'Bitte geben Sie einen Namen ein');
        }
        if (!empty($Name)) {
            $Form->setSuccess('Name', 'Das Privileg wurde hinzugefügt');
            (new Data($this->getBinding()))->createPrivilege($Name);
            return new Redirect('/Platform/Gatekeeper/Authorization/Access/Privilege', 0);
        }
        return $Form;
    }

    /**
     * @param integer $Id
     *
     * @return null|TblPrivilege
     */
    public function getPrivilegeById($Id)
    {

        return (new Data($this->getBinding()))->getPrivilegeById($Id);
    }

    /**
     * @param string $Name
     *
     * @return null|TblPrivilege
     */
    public function getPrivilegeByName($Name)
    {

        return (new Data($this->getBinding()))->getPrivilegeByName($Name);
    }

    /**
     * @return null|TblPrivilege[]
     */
    public function getPrivilegeAll()
    {

        return (new Data($this->getBinding()))->getPrivilegeAll();
    }

    /**
     * @param IFormInterface $Form
     * @param null|string $Name
     *
     * @return IFormInterface|Redirect
     */
    public function createLevel(IFormInterface $Form, $Name)
    {

        if (null !== $Name && empty($Name)) {
            $Form->setError('Name', 'Bitte geben Sie einen Namen ein');
        }
        if (!empty($Name)) {
            $Form->setSuccess('Name', 'Das Zugriffslevel wurde hinzugefügt');
            (new Data($this->getBinding()))->createLevel($Name);
            return new Redirect('/Platform/Gatekeeper/Authorization/Access/Level', 0);
        }
        return $Form;
    }

    /**
     * @param integer $Id
     *
     * @return null|TblLevel
     */
    public function getLevelById($Id)
    {

        return (new Data($this->getBinding()))->getLevelById($Id);
    }

    /**
     * @param string $Name
     *
     * @return null|TblLevel
     */
    public function getLevelByName($Name)
    {

        return (new Data($this->getBinding()))->getLevelByName($Name);
    }

    /**
     * @return null|TblLevel[]
     */
    public function getLevelAll()
    {

        return (new Data($this->getBinding()))->getLevelAll();
    }

    /**
     * Insert TblRole, If not exists
     *
     * @param string $Name
     * @param bool $IsInternal
     * @return null|TblRole|Element
     */
    public function createRole($Name, $IsInternal = false)
    {
        if(!($Entity = $this->getRoleByName($Name))) {
            if(($Entity = (new Data($this->getBinding()))->insertRole($Name, $IsInternal))) {
                return $Entity;
            }
            return null;
        }
        return $Entity;
    }

//    /**
//     * @param IFormInterface $Form
//     * @param null|string $Name
//     *
//     * @return IFormInterface|Redirect
//     */
//    public function createRole(IFormInterface $Form, $Name)
//    {
//
//        if (null !== $Name && empty($Name)) {
//            $Form->setError('Name', 'Bitte geben Sie einen Namen ein');
//        }
//        if (!empty($Name)) {
//            $Form->setSuccess('Name', 'Die Rolle "'.$Name.'" wurde hinzugefügt');
////            (new Data($this->getBinding()))->createRole($Name);
////            return new Redirect('/Platform/Gatekeeper/Authorization/Access/Role', Redirect::TIMEOUT_SUCCESS);
//        }
//        return $Form;
//    }

    /**
     * @param integer $Id
     *
     * @return null|TblRole
     */
    public function getRoleById($Id)
    {

        return (new Data($this->getBinding()))->getRoleById($Id);
    }

    /**
     * @param string $Name
     *
     * @return null|TblRole
     */
    public function getRoleByName($Name)
    {

        return (new Data($this->getBinding()))->getRoleByName($Name);
    }

    /**
     * @return null|TblRole[]
     */
    public function getRoleAll()
    {

        return (new Data($this->getBinding()))->getRoleAll();
    }

    /**
     *
     * @param TblRole $TblRole
     *
     * @return null|TblLevel[]
     */
    public function getLevelAllByRole(TblRole $TblRole)
    {

        return (new Data($this->getBinding()))->getLevelAllByRole($TblRole);
    }

    /**
     *
     * @param TblPrivilege $TblPrivilege
     *
     * @return null|TblRight[]
     */
    public function getRightAllByPrivilege(TblPrivilege $TblPrivilege)
    {

        return (new Data($this->getBinding()))->getRightAllByPrivilege($TblPrivilege);
    }

    /**
     *
     * @param TblLevel $TblLevel
     *
     * @return null|TblPrivilege[]
     */
    public function getPrivilegeAllByLevel(TblLevel $TblLevel)
    {

        return (new Data($this->getBinding()))->getPrivilegeAllByLevel($TblLevel);
    }

    /**
     * @param TblRole $TblRole
     * @param TblLevel $TblLevel
     *
     * @return TblRoleLevel
     */
    public function addRoleLevel(TblRole $TblRole, TblLevel $TblLevel)
    {

        return (new Data($this->getBinding()))->addRoleLevel($TblRole, $TblLevel);
    }

    /**
     * @param TblRole $TblRole
     * @param TblLevel $TblLevel
     *
     * @return bool
     */
    public function removeRoleLevel(TblRole $TblRole, TblLevel $TblLevel)
    {

        return (new Data($this->getBinding()))->removeRoleLevel($TblRole, $TblLevel);
    }

    /**
     * @param TblLevel $TblLevel
     * @param TblPrivilege $TblPrivilege
     *
     * @return bool
     */
    public function removeLevelPrivilege(TblLevel $TblLevel, TblPrivilege $TblPrivilege)
    {

        return (new Data($this->getBinding()))->removeLevelPrivilege($TblLevel, $TblPrivilege);
    }

    /**
     * @param TblPrivilege $TblPrivilege
     * @param TblRight $TblRight
     *
     * @return bool
     */
    public function removePrivilegeRight(TblPrivilege $TblPrivilege, TblRight $TblRight)
    {

        return (new Data($this->getBinding()))->removePrivilegeRight($TblPrivilege, $TblRight);
    }

    /**
     * @param TblPrivilege $TblPrivilege
     * @param TblRight $TblRight
     *
     * @return TblPrivilegeRight
     */
    public function addPrivilegeRight(TblPrivilege $TblPrivilege, TblRight $TblRight)
    {

        return (new Data($this->getBinding()))->addPrivilegeRight($TblPrivilege, $TblRight);
    }

    /**
     * @param TblLevel $TblLevel
     * @param TblPrivilege $TblPrivilege
     *
     * @return TblLevelPrivilege
     */
    public function addLevelPrivilege(TblLevel $TblLevel, TblPrivilege $TblPrivilege)
    {

        return (new Data($this->getBinding()))->addLevelPrivilege($TblLevel, $TblPrivilege);
    }
}
