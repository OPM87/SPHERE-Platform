<?php
namespace SPHERE\Common\Frontend\Layout\Repository;

use SPHERE\Common\Frontend\ITemplateInterface;
use SPHERE\System\Extension\Extension;

/**
 * Class Container
 *
 * @package SPHERE\Common\Frontend\Layout\Structure
 */
class Container extends Extension implements ITemplateInterface
{

    /** @var string $Content */
    private $Content = '';

    /**
     * @param string|array $Content
     */
    public function __construct($Content)
    {
        if( is_array($Content) ) {
            $Content = implode( '', $Content);
        }
        $this->Content = $Content;
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

        return '<div style="margin-bottom: 10px; margin-top: 10px;">'.$this->Content.'</div>';
    }
}
