<?php
abstract class Vpc_Box_Search_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['ajax'] = array(
            'class'     => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Box_Search_Ajax_Component',
            'name'      => 'Ajax'
        );

        $ret['assets']['dep'][] = 'ExtUpdateManager';
        $ret['assets']['dep'][] = 'VpsClearOnFocus';
        $ret['assets']['files'][] = 'vps/Vpc/Box/Search/Component.js';

        $ret['placeholder']['searchButton'] = trlVps('Search');
        $ret['placeholder']['clearOnFocus'] = trlVps('Search term');
        $ret['placeholder']['initialResultText'] = trlVps('Please type at least two characters.');

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['ajaxUrl'] = $this->getData()->getChildComponent('_ajax')->url;
        $formData = $this->getSearchFormData();
        $ret['queryParam'] = $formData['queryParam'];
        $ret['submitParam'] = $formData['submitParam'];
        $ret['searchPageUrl'] = $formData['searchPageUrl'];
        return $ret;
    }

    public function getSearchFormData()
    {
        $fc = $this->_getSearchForm();
        if (!$fc->getComponent() instanceof Vpc_Form_Component) {
            throw new Vps_Exception("The Component returned by _getSearchForm needs to be a Vpc_Form_Component");
        }
        $form = $fc->getComponent()->getForm();
        if (!isset($form->fields['query'])) {
            throw new Vps_Exception("The Form returned by _getSearchForm must have a field named 'query'");
        }
        return array(
            'queryParam' => $form->fields['query']->getFieldName(),
            'submitParam' => $fc->componentId,
            'searchPageUrl' => $fc->url
        );
    }

    abstract public function getSearchComponents();
    abstract protected function _getSearchForm();
}
