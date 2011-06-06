<?php
class Vpc_Directories_List_ViewMap_Coordinates_Component extends Vpc_Abstract_Ajax_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['response'] = array();
        $select = new Vps_Component_Select();
        $select->whereGenerator('detail')
            ->where(new Vps_Model_Select_Expr_Higher('longitude', $this->_getParam('lowestLng')))
            ->where(new Vps_Model_Select_Expr_Higher('latitude', $this->_getParam('lowestLat')))
            ->where(new Vps_Model_Select_Expr_Lower('longitude', $this->_getParam('highestLng')))
            ->where(new Vps_Model_Select_Expr_Lower('latitude', $this->_getParam('highestLat')))
            ->order('name', 'ASC');

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
