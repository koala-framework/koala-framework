<?php
class Vpc_Directories_List_ViewMap_Component extends Vpc_Directories_List_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'vps/Vpc/Directories/List/ViewMap/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'VpsGoogleMap';
        $ret['assets']['dep'][] = 'ExtUtilJson';
        $ret['generators']['child']['component']['paging'] = null;
        $ret['generators']['coordinates'] = array(
            'class'     => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Directories_List_ViewMap_Coordinates_Component',
            'name'      => 'Coordinates'
        );
        $ret['mapOptions'] = array(
            'zoom' => null,
            'zoom_properties' => 0,
            'height' => 400,
            'width' => 550,
            'scale' => 1,
            'satelite' => 1,
            'overview' => 1,
//             'minimumResolution' => 7, // min zoomstufe wenn nötig
//             'maximumResolution' => 12, // max zoomstufe wenn nötig
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['options'] = $this->_getSetting('mapOptions');
        if ($this->getData()->getChildComponent('_coordinates')) {
            $ret['options'] = array_merge(
                $ret['options'],
                array('markers' => $this->getData()->getChildComponent('_coordinates')->getUrl()),
                $this->_noMarkersOptions()
            );
        }
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = parent::getPartialVars($partial, $nr, $info);

        $row = $ret['item']->getRow();
        if (empty($row->coordinates) && (
            !(isset($row->longitude) && isset($row->latitude))
            || (is_null($row->longitude) && is_null($row->latitude))
        )) {
            $marker = false;
        } else {
            $marker = array();
            if (isset($row->longitude) && isset($row->latitude)
                && !is_null($row->longitude) && !is_null($row->latitude)
            ) {
                $marker['latitude']  = $row->latitude;
                $marker['longitude'] = $row->longitude;
            } else if (!empty($row->coordinates)) {
                $coords = explode(';', $row->coordinates);
                $marker['latitude']  = $coords[0];
                $marker['longitude'] = $coords[1];
            } else {
                throw new Vps_Exception('Either longitude and latitude, or coordinates has to exist in model');
            }

            $marker['infoHtml'] = call_user_func_array(
                array($this->getData()->componentClass, 'getInfoWindowHtml'), array($ret['item'])
            );
        }

        $ret['markerData'] = $marker;
        return $ret;
    }

    /**
     * Options that are used if no markers are available
     */
    protected function _noMarkersOptions()
    {
        return array(
            'longitude' => 13.24444771,
            'latitude'  => 47.95334614
        );
    }

    /**
     * Static for performance reasons. Is also called in sub-component "Coordinates"
     */
    static public function getInfoWindowHtml($data)
    {
        $row = $data->getRow();
        $link = new Vps_View_Helper_ComponentLink();
        return $link->componentLinkRendered($data).'<br />'.$row->street.'<br />'.$row->zipcode.' '.$row->city;
    }
}
