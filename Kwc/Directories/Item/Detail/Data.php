<?php
class Kwc_Directories_Item_Detail_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        $ret = parent::__get($var);
        if ($var == 'rel') {
            $ret .= ' kwfDetail'.json_encode(array('directoryComponentId'=>$this->parent->componentId));
        }
        return $ret;
    }
}
