<?php
class Kwc_Directories_List_ViewAjax_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child']['component']['paging'] = 'Kwc_Directories_List_ViewAjax_Paging_Component';

        $ret['assetsDefer']['dep'][] = 'KwfHistoryState';
        $ret['assetsDefer']['dep'][] = 'KwfStatistics';

        $ret['loadMoreBufferPx'] = 700; //if false infinite scrolling is disabled, you still can call loadMore() manually
        $ret['loadDetailAjax'] = true; //true by default - the detail will be loaded via ajax
        $ret['partialClass'] = 'Kwf_Component_Partial_Id';

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $cfg = Kwf_Component_Abstract_ExtConfig_Abstract::getInstance($this->getData()->componentClass);
        $ret['config'] = array(
            'controllerUrl' => $cfg->getControllerUrl('View'),
            'viewUrl' => $this->getData()->url,
            'componentId' => $this->getData()->componentId,
            'searchFormComponentId' => $this->_getSearchForm() ? $this->_getSearchForm()->componentId : null,
            'placeholder' => array(
                'noEntriesFound' => $this->_getPlaceholder('noEntriesFound')
            ),
            'loadMoreBufferPx' => $this->_getSetting('loadMoreBufferPx'),
            'loadDetailAjax' => $this->_getSetting('loadDetailAjax'),
            'limit' => 10,
            'minimumCharactersForFilter' => 3
        );
        $itemDir = $this->getData()->parent->getComponent()->getItemDirectory();
        if (is_string($itemDir)) {
            $ret['config']['directoryViewComponentId'] = false;
            $ret['config']['directoryComponentId'] = false;
            $ret['config']['directoryComponentClass'] = $itemDir;
        } else {
            $ret['config']['directoryViewComponentId'] = $itemDir->getChildComponent('-view')->componentId;
            $ret['config']['directoryComponentId'] = $itemDir->componentId;
            $ret['config']['directoryComponentClass'] = $itemDir->componentClass;
        }


        $paging = $this->getData()->getChildComponent('-paging');
        if (isset($paging)) {
            $ret['config']['initialPageSize'] = Kwc_Abstract::getSetting($paging->componentClass, 'pagesize');
        }

        return $ret;
    }

    //public for ViewController
    public final function getSelect()
    {
        return $this->_getSelect();
    }

    public function getPartialParams()
    {
        $ret = parent::getPartialParams();
        $ret['tpl'] = '<div class="kwfViewAjaxItem {id}">{content}</div>'."\n";
        return $ret;
    }
}
