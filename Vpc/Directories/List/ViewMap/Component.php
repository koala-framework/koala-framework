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
        $ret['generators']['coordinates'] = array(
            'class'     => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Directories_List_ViewMap_Coordinates_Component',
            'name'      => 'Coordinates'
        );
        $ret['mapOptions'] = array(
            'zoom_properties' => 0,
            'height' => 400,
            'width' => 550,
            'scale' => 1,
            'satelite' => 1,
            'overview' => 1,
//             'minimumResolution' => 7, // min zoomstufe wenn nötig
//             'maximumResolution' => 12, // max zoomstufe wenn nötig
        );
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $markers = array();
        foreach ($this->_getItems() as $item) {
            $row = $item->getRow();
            $infoHtml = call_user_func_array(
                array($this->getData()->componentClass, 'getInfoWindowHtml'), array($item)
            );
            $marker = array('infoHtml' => $infoHtml);
            if (empty($row->coordinates) && (
                !(isset($row->longitude) && isset($row->latitude))
                || (is_null($row->longitude) && is_null($row->latitude))
            )) {
                continue;
            }
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
            $markers[] = $marker;
        }

        // calculating the middle
        $lowestLat = $highestLat = $lowestLng = $highestLng = null;
        foreach ($markers as $m) {
            if (is_null($lowestLng) || $lowestLng > $m['longitude']) {
                $lowestLng = $m['longitude'];
            }
            if (is_null($highestLng) || $highestLng < $m['longitude']) {
                $highestLng = $m['longitude'];
            }
            if (is_null($lowestLat) || $lowestLat > $m['latitude']) {
                $lowestLat = $m['latitude'];
            }
            if (is_null($highestLat) || $highestLat < $m['latitude']) {
                $highestLat = $m['latitude'];
            }
        }

        $lightMarkers = $markers;
        $markers = $this->getData()->getChildComponent('_coordinates')->getUrl();

        $ret['options'] = $this->_getSetting('mapOptions');
        if (is_null($lowestLng) || is_null($highestLng) || is_null($lowestLat) || is_null($highestLat)) {
            $ret['options'] = array_merge(
                $ret['options'],
                array('markers' => $markers),
                $this->_noMarkersOptions()
            );
        } else {
            $ret['options'] = array_merge(
                array(
                    'zoom' => array($highestLat, $highestLng, $lowestLat, $lowestLng),
                    'longitude' => ($lowestLng + $highestLng) / 2,
                    'latitude' => ($lowestLat + $highestLat) / 2,
                    'markers' => $markers,
                    'lightMarkers' => $lightMarkers
                ),
                $ret['options']
            );
        }
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
        return $link->componentLink($data).'<br />'.$row->street.'<br />'.$row->zipcode.' '.$row->city;
    }
}
