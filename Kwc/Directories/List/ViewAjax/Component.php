<?php
class Kwc_Directories_List_ViewAjax_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child']['component']['paging'] = 'Kwc_Directories_List_ViewAjax_Paging_Component';

        $ret['assets']['files'][] = 'kwf/Kwc/Directories/List/ViewAjax/Component.js';
        $ret['assets']['dep'][] = 'KwfAutoGrid'; //TODO: less dep
        $ret['assets']['dep'][] = 'KwfHistoryState';
        $ret['assets']['dep'][] = 'KwfStatistics';
        $ret['assets']['dep'][] = 'KwfOnReady';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($this->getData()->parent
            ->getComponent()
            ->getItemDirectory()
            ->getChildComponent('-view')
            ->componentClass
            != $this->getData()->componentClass
        ) {
//             throw new Kwf_Exception('Invalid View: must be the same as the one used for the directory itself if using ViewAjax');
        }

        $cfg = Kwf_Component_Abstract_ExtConfig_Abstract::getInstance($this->getData()->componentClass);
        $ret['config'] = array(
            'controllerUrl' => $cfg->getControllerUrl('View'),
            'directoryViewComponentId' => $this->getData()->parent->getComponent()->getItemDirectory()->getChildComponent('-view')->componentId,
            'viewUrl' => $this->getData()->url,
            'directoryUrl' => $this->getData()->parent->getComponent()->getItemDirectory()->url,
            'componentId' => $this->getData()->componentId,
            'directoryComponentId' => $this->getData()->parent->getComponent()->getItemDirectory()->componentId,
            'searchFormComponentId' => $this->_getSearchForm() ? $this->_getSearchForm()->componentId : null,
            'placeholder' => array(
                'noEntriesFound' => $this->_getPlaceholder('noEntriesFound')
            )
        );
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
