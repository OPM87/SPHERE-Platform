<?php
namespace SPHERE\Application\Platform\Gatekeeper\Authorization\Account;

use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Access;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Access\Service\Entity\TblRole;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Service\Entity\TblAccount;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Service\Entity\TblIdentification;
use SPHERE\Application\Platform\Gatekeeper\Consumer\Consumer;
use SPHERE\Common\Frontend\Form\Repository\Button\Primary;
use SPHERE\Common\Frontend\Form\Repository\Field\CheckBox;
use SPHERE\Common\Frontend\Form\Repository\Field\PasswordField;
use SPHERE\Common\Frontend\Form\Repository\Field\RadioBox;
use SPHERE\Common\Frontend\Form\Repository\Field\TextField;
use SPHERE\Common\Frontend\Form\Structure\Form;
use SPHERE\Common\Frontend\Form\Structure\FormColumn;
use SPHERE\Common\Frontend\Form\Structure\FormGroup;
use SPHERE\Common\Frontend\Form\Structure\FormRow;
use SPHERE\Common\Frontend\Icon\Repository\Lock;
use SPHERE\Common\Frontend\Icon\Repository\Person;
use SPHERE\Common\Frontend\Icon\Repository\Remove;
use SPHERE\Common\Frontend\Icon\Repository\Repeat;
use SPHERE\Common\Frontend\Layout\Repository\Panel;
use SPHERE\Common\Frontend\Link\Repository\Danger;
use SPHERE\Common\Frontend\Message\Repository\Warning;
use SPHERE\Common\Frontend\Table\Repository\Title;
use SPHERE\Common\Frontend\Table\Structure\Table;
use SPHERE\Common\Window\Stage;

/**
 * Class Frontend
 *
 * @package SPHERE\Application\System\Gatekeeper\Authorization\Account
 */
class Frontend
{

    /**
     * @return Stage
     */
    public function frontendAccount()
    {

        $Stage = new Stage('Benutzerkonten');
        $Stage->hasUtilityFavorite(true);

        $TblAccount = Account::useRead()->getAccountBySession();
        if ($TblAccount) {
            $isSystem = Account::useService()->hasAuthorization(
                $TblAccount, Access::useService()->getRoleByName('Administrator')
            );
        } else {
            $isSystem = false;
        }
        $TblConsumer = Consumer::useService()->getConsumerBySession();

        // Identification
        $TblIdentificationAll = Account::useService()->getIdentificationAll();
        if ($TblIdentificationAll) {
            /** @noinspection PhpUnusedParameterInspection */
            array_walk($TblIdentificationAll, function (TblIdentification &$TblIdentification, $Index, $isSystem) {

                if ($TblIdentification->getName() == 'System' && !$isSystem) {
                    $TblIdentification = false;
                } else {
                    $TblIdentification = new RadioBox(
                        'Account[Identification]', $TblIdentification->getDescription(), $TblIdentification->getId()
                    );
                }
            }, $isSystem);
            $TblIdentificationAll = array_filter($TblIdentificationAll);
        } else {
            $TblIdentificationAll = array();
        }

        // Role
        $TblRoleAll = Access::useService()->getRoleAll();
        if ($TblRoleAll) {
            /** @noinspection PhpUnusedParameterInspection */
            array_walk($TblRoleAll, function (TblRole &$TblRole, $Index, $isSystem) {

                if ($TblRole->getName() == 'Administrator' && !$isSystem) {
                    $TblRole = false;
                } else {
                    $TblRole = new CheckBox('Account[Role][' . $TblRole->getId() . ']', $TblRole->getName(),
                        $TblRole->getId());
                }
            }, $isSystem);
            $TblRoleAll = array_filter($TblRoleAll);
        } else {
            $TblRoleAll = array();
        }
        // Account
        $TblAccountAll = Account::useService()->getAccountAll();
        if ($TblAccountAll) {
            array_walk($TblAccountAll, function (TblAccount &$TblAccount) {

                /** @noinspection PhpUndefinedFieldInspection */
                $TblAccount->Option = new Danger('Löschen',
                    '/Platform/Gatekeeper/Authorization/Account/Destroy',
                    new Remove(), array('Id' => $TblAccount->getId()), 'Löschen'
                );
            });
        }

        $Stage->setContent(
            ($TblAccountAll
                ? new Table($TblAccountAll, new Title('Bestehende Benutzerkonten'), array(
                    'Username' => 'Benutzername',
//                    'Option' => 'Optionen'
                ))
                : new Warning('Keine Benutzerkonten vorhanden')
            )
            //.Account::useService()->createAccount(
            . new Form(array(
                new FormGroup(array(
                    new FormRow(array(
                        new FormColumn(
                            (new TextField('Account[Name]', 'Benutzername', 'Benutzername', new Person()))
                                ->setPrefixValue($TblConsumer->getAcronym())->setAutoFocus()->setRequired()
                            , 4),
                        new FormColumn(
                            (new PasswordField(
                                'Account[Password]', 'Passwort', 'Passwort', new Lock()
                            ))->setRequired(), 4),
                        new FormColumn(
                            (new PasswordField(
                                'Account[PasswordSafety]', 'Passwort wiederholen', 'Passwort wiederholen',
                                new Repeat()
                            ))->setRequired(), 4),
                    )),
                ), new \SPHERE\Common\Frontend\Form\Repository\Title('Benutzerkonto anlegen')),
                new FormGroup(array(
                    new FormRow(array(
                        new FormColumn(array(
                            new Panel('Authentifizierungstyp', $TblIdentificationAll)
                        ), 4),
                        new FormColumn(array(
                            new Panel('Berechtigungsstufe', $TblRoleAll)
                        ), 4),
                    ))

                ), new \SPHERE\Common\Frontend\Form\Repository\Title('Berechtigungen zuweisen')),
            ), new Primary('Hinzufügen'))
        );
        return $Stage;
    }
}
