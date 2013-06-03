<?php
abstract class Kwc_Box_Search_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['ajax'] = array(
            'class'     => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Box_Search_Ajax_Component',
            'name'      => 'Ajax'
        );

        $ret['assets']['dep'][] = 'ExtUpdateManager';
        $ret['assets']['dep'][] = 'KwfClearOnFocus';
        $ret['assets']['dep'][] = 'ExtDelayedTask';
        $ret['assets']['dep'][] = 'KwfOnReady';
        $ret['assets']['files'][] = 'kwf/Kwc/Box/Search/Component.js';

        $ret['placeholder']['searchButton'] = trlKwfStatic('Search');
        $ret['placeholder']['clearOnFocus'] = trlKwfStatic('Search term');
        $ret['placeholder']['initialResultText'] = trlKwfStatic('Please type at least two characters.');

        $ret['searchResultBoxAlign'] = 'tl-bl';
        $ret['searchResultBoxFade'] = true;

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
        $ret['searchSettings'] = array(
            'searchResultBoxAlign' => $this->_getSetting('searchResultBoxAlign'),
            'searchResultBoxFade' => $this->_getSetting('searchResultBoxFade')
        );
        return $ret;
    }

    public function getSearchFormData()
    {
        $fc = $this->_getSearchForm();
        if (!$fc->getComponent() instanceof Kwc_Form_Component) {
            throw new Kwf_Exception("The Component returned by _getSearchForm needs to be a Kwc_Form_Component");
        }
        $form = $fc->getComponent()->getForm();
        if (!isset($form->fields['query'])) {
            throw new Kwf_Exception("The Form returned by _getSearchForm must have a field named 'query'");
        }
        $ret = array(
            'queryParam' => $form->fields['query']->getFieldName(),
            'submitParam' => $fc->componentId,
            'searchPageUrl' => $fc->url,
            'queryValue' => null
        );
        if ($fc->getComponent()->isProcessed()) {
            $ret['queryValue'] = $fc->getComponent()->getFormRow()->query;
        }
        return $ret;
    }

    /**
     * Überschreiben, um Views, die eine SearchForm haben (müssen alle die gleiche
     * haben) zurückgibt.
     *
     * @return array mit Views, kann assoziativ sein (mit Titel der List)
     */
    abstract public function getSearchViews();

    protected function _getSearchForm()
    {
        return current($this->getSearchViews())->getComponent()->getSearchForm();
    }
}
