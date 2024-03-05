<?php
class Kwc_Directories_List_ViewMap_MarkersController extends Kwf_Controller_Action
{
    public function jsonIndexAction()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('componentId'), array('ignoreVisible' => true));

        $view = $component->getComponent();
        $baseSelect = $view->getSelect();
        if ($view->hasSearchForm()) {
            $sf = $view->getSearchForm();
            $params = $this->getRequest()->getParams();
            $params[$sf->componentId.'-post'] = true; //post
            $params[$sf->componentId] = true; //submit
            $sf->getComponent()->processInput($params); //TODO don't do processInput here in jsonIndexAction() same problem in AjaxView
        }
        $select = $view->getSelect();

        if ($this->_getParam('lowestLng')) {
            $select->where(new Kwf_Model_Select_Expr_Higher('longitude', $this->_getParam('lowestLng')));
            $baseSelect->where(new Kwf_Model_Select_Expr_Higher('longitude', $this->_getParam('lowestLng')));
        }
        if ($this->_getParam('lowestLat')) {
            $select->where(new Kwf_Model_Select_Expr_Higher('latitude', $this->_getParam('lowestLat')));
            $baseSelect->where(new Kwf_Model_Select_Expr_Higher('latitude', $this->_getParam('lowestLat')));
        }
        if ($this->_getParam('highestLng')) {
            $select->where(new Kwf_Model_Select_Expr_Lower('longitude', $this->_getParam('highestLng')));
            $baseSelect->where(new Kwf_Model_Select_Expr_Lower('longitude', $this->_getParam('highestLng')));
        }
        if ($this->_getParam('highestLat')) {
            $select->where(new Kwf_Model_Select_Expr_Lower('latitude', $this->_getParam('highestLat')));
            $baseSelect->where(new Kwf_Model_Select_Expr_Lower('latitude', $this->_getParam('highestLat')));
        }

        $itemDirectory = $component->getParent()->getComponent()->getItemDirectory();
        $items = $itemDirectory->getChildComponents($select);

        $markerIds = array();
        $markers = array();
        $parentComponentClass = $component->componentClass;
        foreach ($items as $item) {
            $markerIds[] = $item->row->id;
            $markers[] = array(
                'latitude'  => $item->row->latitude,
                'longitude' => $item->row->longitude,
                'infoHtml'  => call_user_func_array(
                    array($parentComponentClass, 'getInfoWindowHtml'), array($item)
                ),
                'isLightMarker' => true
            );
        }

        $mapOptions = Kwc_Abstract::getSetting($component->componentClass, 'mapOptions');
        if ($mapOptions['showAlwaysAllMarkers']) {
            if ($markerIds) {
                $baseSelect->whereNotEquals('id', $markerIds);
            }
            $otherMarkers = array();
            foreach ($itemDirectory->getChildComponents($baseSelect) as $item) {
                $otherMarkers[] = array(
                    'latitude'  => $item->row->latitude,
                    'longitude' => $item->row->longitude,
                    'infoHtml'  => call_user_func_array(
                        array($parentComponentClass, 'getInfoWindowHtml'), array($item)
                    ),
                    'isLightMarker' => false
                );
            }
            $markers = array_merge($markers, $otherMarkers);
        }
        $this->view->count = count($markers);
        $this->view->markers = $markers;
    }

    protected function _isAllowedComponent()
    {
        return true;
    }
}
