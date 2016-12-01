<?php
class Kwc_Directories_List_ViewMap_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = null;

        $ret['mapOptions']['zoom'] = null;
        $ret['mapOptions']['zoomProperties'] = 0;
        $ret['mapOptions']['height'] = 400;
        $ret['mapOptions']['width'] = 550;
        $ret['mapOptions']['scale'] = 1;
        $ret['mapOptions']['satelite'] = 1;
        $ret['mapOptions']['overview'] = 1;
        $ret['mapOptions']['latitude'] = 47.95334614; // if dynamicStartPos = true this is center if no markers available, else this
        $ret['mapOptions']['longitude'] = 13.24444771; // point is centered
        $ret['mapOptions']['showAlwaysAllMarkers'] = true; // if true searchResults are shown blue, rest is red

        $ret['optimizedMobileUI'] = false;
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        foreach ($settings['mapOptions'] as $key => $value) {
            if (strpos('_', $key) !== false) throw new Kwf_Exception('mapOptions must be camelCase');
        }
    }

    public function getSelect()
    {
        return $this->_getSelect();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['options'] = $this->_getSetting('mapOptions');
        if ($this->_getSearchForm()) {
            if (!Kwc_Abstract::getSetting($this->_getSearchForm()->componentClass, 'useAjaxRequest')) {
                $e = new Kwf_Exception_NotYetImplemented();
                $e->logOrThrow();
            }
            $ret['options']['searchFormComponentId'] = $this->_getSearchForm()->componentId;
        }
        $ret['options']['baseParams']['componentId'] = $this->getData()->componentId;
        $ret['options']['markers'] = Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl('Markers').'/json-index';
        $ret['optimizedMobileUI'] = $this->_getSetting('optimizedMobileUI');
        return $ret;
    }

    /**
     * Static for performance reasons. Is also called in sub-component "Coordinates"
     */
    static public function getInfoWindowHtml($data)
    {
        return "<a href=\"$data->url\" rel=\"$data->rel\">$data->name</a>";
    }
}
