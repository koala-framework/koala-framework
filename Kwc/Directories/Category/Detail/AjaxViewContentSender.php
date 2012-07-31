<?php
class Kwc_Directories_Category_Detail_AjaxViewContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function getLinkRel()
    {
        $ret = parent::getLinkRel();
        $view = $this->_data->getChildComponent('-list')->getComponent()
            ->getItemDirectory()->getChildComponent('-view');
        $config = array(
            'componentId' => $this->_data->componentId,
            'viewComponentId' => $view->componentId
        );
        $ret .= ' kwfViewAjaxFilter'.json_encode($config);
        return trim($ret);
    }
}
