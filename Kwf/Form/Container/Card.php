<?php
/**
 * @package Form
 */
class Kwf_Form_Container_Card extends Kwf_Form_Container_Abstract
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setBaseCls('x2-plain');
        $this->setAutoHeight(true);
    }
    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'title';
        return $ret;
    }
}
