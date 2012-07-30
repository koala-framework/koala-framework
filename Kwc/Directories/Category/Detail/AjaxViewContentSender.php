<?php
class Kwc_Directories_Category_Detail_AjaxViewContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function getLinkRel()
    {
        $ret = parent::getLinkRel();
        $config = array(
            'componentId' => $this->_data->componentId,
            'viewComponentId' => 'root_directory-view' //TODO dynamic obviously
        );
        $ret .= ' kwfViewAjaxFilter'.json_encode($config);
        return trim($ret);
    }
}
