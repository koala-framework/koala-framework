<?php
class Kwc_Menu_Expanded_ParentMenu_Component extends Kwc_Menu_ParentMenu_Component
{
    protected function _processMenuSetCurrent(&$ret)
    {
        parent::_processMenuSetCurrent($ret);
        foreach ($ret as $k=>&$i) {
            if (isset($i['submenu'])) {
                $this->_processMenuSetCurrent($i['submenu']);
            }
        }
    }
}
