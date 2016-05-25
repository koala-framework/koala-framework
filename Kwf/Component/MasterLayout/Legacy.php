<?php
class Kwf_Component_MasterLayout_Legacy extends Kwf_Component_MasterLayout_Abstract
{
    public function getContexts(Kwf_Component_Data $data)
    {
        return null;
    }

    public function getContentWidth(Kwf_Component_Data $data)
    {
        if (!$this->_hasSetting('contentWidth')) {
            throw new Kwf_Exception("contentWidth has to be set for $data->componentClass (getting width for $data->componentId)");
        }
        $ret = $this->_getSetting('contentWidth');
        if (!$this->_hasSetting('contentWidthBoxSubtract')) return $ret;

        $boxes = array();
        foreach ($data->getChildBoxes() as $box) {
            $boxes[$box->box] = $box;
        }
        if ($this->_hasSetting('contentWidthBoxSubtract')) {
            foreach ($this->_getSetting('contentWidthBoxSubtract') as $box=>$width) {
                if (!isset($boxes[$box])) continue;
                $c = $boxes[$box];
                if ($c && $c->hasContent()) {
                    $ret -= $width;
                }
            }
        }
        return $ret;
    }
}
