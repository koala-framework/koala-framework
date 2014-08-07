<?php
class Kwc_Directories_List_ViewMap_MarkersController extends Kwf_Controller_Action
{
    public function jsonIndexAction()
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('componentId'), array('ignoreVisible' => true));

        $view = $component->getComponent();
        if ($view->hasSearchForm()) {
            $sf = $view->getSearchForm();
            $params = $this->getRequest()->getParams();
            $params[$sf->componentId.'-post'] = true; //post
            $params[$sf->componentId] = true; //submit
            $sf->getComponent()->processInput($params); //TODO don't do processInput here in _getSelect()
        }
        $select = $view->getSelect();

        if (isset($_REQUEST['lowestLng'])) {
            $select->where(new Kwf_Model_Select_Expr_Higher('longitude', $_REQUEST['lowestLng']));
        }
        if (isset($_REQUEST['lowestLat'])) {
            $select->where(new Kwf_Model_Select_Expr_Higher('latitude', $_REQUEST['lowestLat']));
        }
        if (isset($_REQUEST['highestLng'])) {
            $select->where(new Kwf_Model_Select_Expr_Lower('longitude', $_REQUEST['highestLng']));
        }
        if (isset($_REQUEST['highestLat'])) {
            $select->where(new Kwf_Model_Select_Expr_Lower('latitude', $_REQUEST['highestLat']));
        }
        $itemDirectory = $component->getParent()->getComponent()->getItemDirectory();
        $items = $itemDirectory->getChildComponents($select);
        $this->view->count = count($items);

        $markers = array();
        $parentComponentClass = $component->componentClass;
        foreach ($items as $item) {
            $markers[] = array(
                'latitude'  => $item->row->latitude,
                'longitude' => $item->row->longitude,
                'infoHtml'  => call_user_func_array(
                    array($parentComponentClass, 'getInfoWindowHtml'), array($item)
                )
            );
        }
        $this->view->markers = $markers;
    }
}
