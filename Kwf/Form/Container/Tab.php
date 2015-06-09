<?php
/**
 * @package Form
 */
class Kwf_Form_Container_Tab extends Kwf_Form_Container_Abstract
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setBaseCls('x2-plain');
        $this->setAutoHeight(true);
        $this->setBodyStyle('padding:10px');
    }
}
