<?php
namespace SPHERE\Application\Platform\System\Protocol;

use SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Account;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Service\Entity\TblAccount;
use SPHERE\Application\Platform\System\Protocol\Service\Data;
use SPHERE\Application\Platform\System\Protocol\Service\Entity\LoginAttemptHistory;
use SPHERE\Application\Platform\System\Protocol\Service\Entity\TblProtocol;
use SPHERE\Application\Platform\System\Protocol\Service\Setup;
use SPHERE\System\Database\Binding\AbstractService;
use SPHERE\System\Database\Fitting\Element;

/**
 * Class Service
 *
 * @package SPHERE\Application\System\Platform\Protocol
 */
class Service extends AbstractService
{

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
     * Get available Database-Name-List
     *
     * (Distinct)
     *
     * @return array
     */
    public function getProtocolDatabaseNameList()
    {

        return (new Data($this->getBinding()))->getProtocolDatabaseNameList();
    }

    /**
     * @return bool|TblProtocol[]
     */
    public function getProtocolAll()
    {

        return (new Data($this->getBinding()))->getProtocolAll();
    }

    /**
     * @param TblAccount $TblAccount
     *
     * @return bool|TblProtocol[]
     */
    public function getProtocolLastActivity(TblAccount $TblAccount)
    {

        return (new Data($this->getBinding()))->getProtocolLastActivity($TblAccount);
    }

    /**
     * @return TblProtocol[]|bool
     */
    public function getProtocolAllCreateSession()
    {

        return (new Data($this->getBinding()))->getProtocolAllCreateSession();
    }

    /**
     * @param string|null $CredentialName
     * @param string|null $CredentialLock
     * @param string|null $CredentialKey
     *
     * @return false|TblProtocol
     */
    public function createLoginAttemptEntry(
        $CredentialName,
        $CredentialLock,
        $CredentialKey = null
    ) {

        $TblAccount = Account::useService()->getAccountBySession();
        if ($TblAccount) {
            $TblConsumer = $TblAccount->getServiceTblConsumer();
        } else {
            $TblConsumer = null;
        }

        $Entity = new LoginAttemptHistory();
        $Entity->setCredentialName($CredentialName);
        $Entity->setCredentialLock($CredentialLock);
        $Entity->setCredentialKey($CredentialKey);

        return (new Data($this->getBinding()))->createProtocolEntry(
            'LoginAttemptHistory',
            ($TblAccount ? $TblAccount : null),
            ($TblConsumer ? $TblConsumer : null),
            null,
            $Entity
        );
    }

    /**
     * @param string $DatabaseName
     * @param Element $Entity
     * @param bool $useBulkSave MUST call "flushBulkEntries" if true
     *
     * @return false|TblProtocol
     */
    public function createInsertEntry(
        $DatabaseName,
        Element $Entity,
        $useBulkSave = false
    ) {

        $TblAccount = Account::useService()->getAccountBySession();
        if ($TblAccount) {
            $TblConsumer = $TblAccount->getServiceTblConsumer();
        } else {
            $TblConsumer = null;
        }

        return (new Data($this->getBinding()))->createProtocolEntry(
            $DatabaseName,
            ($TblAccount ? $TblAccount : null),
            ($TblConsumer ? $TblConsumer : null),
            null,
            $Entity,
            $useBulkSave
        );
    }

    /**
     * @param string $DatabaseName
     * @param Element $From
     * @param Element $To
     * @param bool $useBulkSave MUST call "flushBulkEntries" if true
     *
     * @return false|TblProtocol
     */
    public function createUpdateEntry(
        $DatabaseName,
        Element $From,
        Element $To,
        $useBulkSave = false
    ) {

        $TblAccount = Account::useService()->getAccountBySession();
        if ($TblAccount) {
            $TblConsumer = $TblAccount->getServiceTblConsumer();
        } else {
            $TblConsumer = null;
        }

        if (($Protocol = (new Data($this->getBinding()))->createProtocolEntry(
            $DatabaseName,
            ($TblAccount ? $TblAccount : null),
            ($TblConsumer ? $TblConsumer : null),
            $From,
            $To,
            $useBulkSave
        ))
        ) {

        };
        return $Protocol;
    }

    /**
     * @param string $DatabaseName
     * @param Element $Entity
     * @param bool $useBulkSave MUST call "flushBulkEntries" if true
     *
     * @return false|TblProtocol
     */
    public function createDeleteEntry(
        $DatabaseName,
        Element $Entity = null,
        $useBulkSave = false
    ) {

        $TblAccount = Account::useService()->getAccountBySession();
        if ($TblAccount) {
            $TblConsumer = $TblAccount->getServiceTblConsumer();
        } else {
            $TblConsumer = null;
        }

        return (new Data($this->getBinding()))->createProtocolEntry(
            $DatabaseName,
            ($TblAccount ? $TblAccount : null),
            ($TblConsumer ? $TblConsumer : null),
            $Entity,
            null,
            $useBulkSave
        );
    }

    /**
     * MUST call if "useBulkSave" parameter was used with
     * - createDeleteEntry
     * - createUpdateEntry
     * - createInsertEntry
     */
    public function flushBulkEntries()
    {
        (new Data($this->getBinding()))->flushBulkSave();
    }
}
