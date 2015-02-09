<?php
class Kwc_Directories_Item_Detail_Data extends Kwf_Component_Data
{
    public function getLinkDataAttributes()
    {
        $ret = parent::getLinkDataAttributes();
        $ret['kwc-detail'] = json_encode(array(
            'directoryComponentId'    => $this->parent->componentId,
            'directoryComponentClass' => $this->parent->componentClass
        ));
        return $ret;
    }
}
