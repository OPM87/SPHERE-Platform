<?php
namespace SPHERE\Common\Frontend\Message\Repository;

use MOC\V\Component\Template\Component\IBridgeInterface;
use SPHERE\Common\Frontend\Icon\IIconInterface;
use SPHERE\Common\Frontend\Message\IMessageInterface;
use SPHERE\System\Extension\Extension;

/**
 * Class Info
 *
 * @package SPHERE\Common\Frontend\Message\Repository
 */
class Info extends Extension implements IMessageInterface
{

    /** @var IBridgeInterface $Template */
    private $Template = null;

    /**
     * @param string         $Content
     * @param IIconInterface $Icon
     * @param bool           $Toggle
     */
    public function __construct($Content, IIconInterface $Icon = null, $Toggle = false)
    {

        $this->Template = $this->getTemplate(__DIR__.'/Message.twig');
        $this->Template->setVariable('Type', 'info');
        $this->Template->setVariable('Content', $Content);
        if (null !== $Icon) {
            $this->Template->setVariable('Icon', $Icon);
        }
        $this->Template->setVariable('Hash', md5(uniqid(__METHOD__, true)));
        $this->Template->setVariable('Toggle', $Toggle);
    }

    /**
     * @return string
     */
    public function __toString()
    {

        return $this->getContent();
    }

    /**
     * @return string
     */
    public function getContent()
    {

        return $this->Template->getContent();
    }

    /**
     * @return string
     */
    public function getName()
    {

        return null;
    }
}
