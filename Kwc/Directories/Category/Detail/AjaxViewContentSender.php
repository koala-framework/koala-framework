<?php
class Kwc_Directories_Category_Detail_AjaxViewContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function getLinkDataAttributes()
    {
        $ret = parent::getLinkDataAttributes();
        $view = $this->_data->getChildComponent('-list')->getComponent()
            ->getItemDirectory()->getChildComponent('-view');
        $config = array(
            'componentId' => $this->_data->componentId,
            'viewComponentId' => $view->componentId
        );
        $ret['kwc-view-ajax-filter'] = json_encode($config);
        return $ret;
    }
}
