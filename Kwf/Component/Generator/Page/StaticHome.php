<?php
class Kwf_Component_Generator_Page_StaticHome extends Kwf_Component_Generator_Page_Static
{
    protected function _init()
    {
        parent::_init();
        if (count($this->_settings['component']) > 1) {
            throw new Kwf_Exception("StaticHome generator must have only a single component");
        }
    }

    protected function _formatSelectHome(Kwf_Component_Select $select)
    {
        return $select;
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['hasHome'] = true;
        return $ret;
    }
}
