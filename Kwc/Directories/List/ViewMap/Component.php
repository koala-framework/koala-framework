<?php
class Kwc_Directories_List_ViewMap_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsDefer']['dep'][] = 'KwfGoogleMap';
        $ret['assetsDefer']['dep'][] = 'ExtUtilJson';
        $ret['assetsDefer']['dep'][] = 'ExtConnection';
        $ret['generators']['child']['component']['paging'] = null;
        $ret['mapOptions'] = array(
            'zoom' => null,
            'zoom_properties' => 0,
            'height' => 400,
            'width' => 550,
            'scale' => 1,
            'satelite' => 1,
            'overview' => 1,
            'dynamicStartPos' => true, // if set to true, center of shown coordinates is used
            'latitude' => 47.95334614,  // if dynamicStartPos = true this is center if no markers available, else this
            'longitude' => 13.24444771, // point is centered
            'showAlwaysAllMarkers' => true // if true searchResults are shown blue, rest is red
        );
        return $ret;
    }

    public function getSelect()
    {
        return $this->_getSelect();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['options'] = $this->_getSetting('mapOptions');
        $ret['options']['searchFormComponentId'] = $this->_getSearchForm() ? $this->_getSearchForm()->componentId : null;
        $ret['options']['componentId'] = $this->getData()->componentId;
        $ret['options']['markers'] = Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl('Markers').'/json-index';
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
