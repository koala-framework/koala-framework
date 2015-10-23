<?php
class Kwc_Directories_List_ViewMap_Coordinates_Component extends Kwc_Abstract_Ajax_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['response'] = array();
        $select = new Kwf_Component_Select();
        $lowestLng = isset($_REQUEST['lowestLng']) ? $_REQUEST['lowestLng'] : null;
        $lowestLat = isset($_REQUEST['lowestLat']) ? $_REQUEST['lowestLat'] : null;
        $highestLng = isset($_REQUEST['highestLng']) ? $_REQUEST['highestLng'] : null;
        $highestLat = isset($_REQUEST['highestLat']) ? $_REQUEST['highestLat'] : null;
        $select->whereGenerator('detail')
            ->where(new Kwf_Model_Select_Expr_Higher('longitude', $lowestLng))
            ->where(new Kwf_Model_Select_Expr_Higher('latitude', $lowestLat))
            ->where(new Kwf_Model_Select_Expr_Lower('longitude', $highestLng))
            ->where(new Kwf_Model_Select_Expr_Lower('latitude', $highestLat));

        $parentComponentClass = $this->getData()->parent->componentClass;
        $itemDirectory = $this->getData()->parent->parent->getComponent()->getItemDirectory();
        $itemCount = $itemDirectory->countChildComponents($select);
        $ret['response']['count'] = $itemCount;
        $ret['response']['markers'] = array();

        $items = $itemDirectory->getChildComponents($select);
        foreach ($items as $item) {
            $ret['response']['markers'][] = array(
                'latitude'  => $item->row->latitude,
                'longitude' => $item->row->longitude,
                'infoHtml'  => call_user_func_array(
                    array($parentComponentClass, 'getInfoWindowHtml'), array($item)
                )
            );
        }
        return $ret;
    }

}
