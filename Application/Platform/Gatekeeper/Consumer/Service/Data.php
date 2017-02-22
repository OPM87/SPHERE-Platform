<?php
namespace SPHERE\Application\Platform\Gatekeeper\Consumer\Service;

use SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Account;
use SPHERE\Application\Platform\Gatekeeper\Consumer\Service\Entity\TblConsumer;
use SPHERE\Application\Platform\System\Protocol\Protocol;
use SPHERE\System\Cache\Handler\MemoryHandler;
use SPHERE\System\Database\Binding\AbstractData;

/**
 * Class Data
 *
 * @package SPHERE\Application\Platform\Gatekeeper\Authorization\Consumer\Service
 */
class Data extends AbstractData
{
    /**
     * @return void
     */
    public function setupDatabaseContent()
    {

        $this->createConsumer('DEMO', 'Mandant');
    }

    /**
     * @param string $Acronym
     * @param string $Name
     *
     * @return TblConsumer
     */
    public function createConsumer($Acronym, $Name)
    {

        $Manager = $this->getEntityManager();
        $Entity = $Manager->getEntity('TblConsumer')
            ->findOneBy(array(TblConsumer::ATTR_ACRONYM => $Acronym));
        if (null === $Entity) {
            $Entity = new TblConsumer();
            $Entity->setAcronym($Acronym);
            $Entity->setName($Name);
            $Manager->saveEntity($Entity);
            Protocol::useService()->createInsertEntry($this->getConnection()->getDatabase(), $Entity);
        }
        return $Entity;
    }

    /**
     * @param string $Name
     *
     * @return null|TblConsumer
     */
    public function getConsumerByName($Name)
    {

        return $this->getCachedEntityBy(__METHOD__, $this->getEntityManager(), 'TblConsumer', array(
            TblConsumer::ATTR_NAME => $Name
        ));
    }

    /**
     * @param string $Acronym
     *
     * @return null|TblConsumer
     */
    public function getConsumerByAcronym($Acronym)
    {

        return $this->getCachedEntityBy(__METHOD__, $this->getEntityManager(), 'TblConsumer', array(
            TblConsumer::ATTR_ACRONYM => $Acronym
        ));
    }

    /**
     * @param integer $Id
     *
     * @return null|TblConsumer
     */
    public function getConsumerById($Id)
    {

        return $this->getCachedEntityById(__METHOD__, $this->getEntityManager(), 'TblConsumer', $Id);
    }

    /**
     * @return TblConsumer[]|null
     */
    public function getConsumerAll()
    {

        return $this->getCachedEntityList(__METHOD__, $this->getEntityManager(), 'TblConsumer');
    }

    /**
     * @param null|string $Session
     *
     * @return bool|TblConsumer
     */
    public function getConsumerBySession($Session = null)
    {

        // 1. Level Cache
        $Memory = $this->getCache(new MemoryHandler());
        if (null === ($Entity = $Memory->getValue($Session, __METHOD__))) {

            if (($TblAccount = Account::useRead()->getAccountBySession($Session))) {
                $Entity = $TblAccount->getServiceTblConsumer();
            } else {
                $Entity = false;
            }
            $Memory->setValue($Session, $Entity, 0, __METHOD__);
        }
        return $Entity;
    }
}
