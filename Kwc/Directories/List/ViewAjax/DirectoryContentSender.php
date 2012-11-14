<?php
class Kwc_Directories_List_ViewAjax_DirectoryContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function getLinkRel()
    {
        $ret = parent::getLinkRel();
        $config = array(
            'componentId' => $this->_data->componentId,
            'viewComponentId' => $this->_data->getChildComponent('-view')->componentId
        );
        $ret .= ' kwfViewAjaxFilter'.json_encode($config);
        return trim($ret);
    }
}
