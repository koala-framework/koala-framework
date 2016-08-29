<?php
class Kwc_Directories_List_ViewAjax_DirectoryContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function getLinkDataAttributes()
    {
        $ret = parent::getLinkDataAttributes();
        $config = array(
            'componentId' => $this->_data->componentId,
            'viewComponentId' => $this->_data->getChildComponent('-view')->componentId
        );
        $ret['kwc-view-ajax-filter'] = json_encode($config);
        return $ret;
    }

    public function getLinkClass()
    {
        return 'kwfUp-kwcViewAjaxFilter';
    }
}
