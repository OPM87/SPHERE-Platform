<?php
namespace SPHERE\Common\Frontend\Link\Repository;

use SPHERE\Common\Frontend\Icon\IIconInterface;
use SPHERE\Common\Frontend\Link\ILinkInterface;

/**
 * Class Link
 *
 * @package SPHERE\Common\Frontend\Link\Repository
 */
class Link extends AbstractLink implements ILinkInterface
{

    /**
     * AbstractLink constructor.
     *
     * @param string              $Name
     * @param string              $Path
     * @param IIconInterface|null $Icon
     * @param array               $Data
     * @param bool|string         $ToolTip
     * @param null|string         $Anchor
     */
    public function __construct($Name, $Path, IIconInterface $Icon = null, $Data = array(), $ToolTip = false, $Anchor = null)
    {

        $this->setType(self::TYPE_LINK);
        parent::__construct($Name, $Path, $Icon, $Data, $ToolTip, $Anchor);
    }
}
