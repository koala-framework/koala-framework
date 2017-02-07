<?php
class Kwc_Basic_LinkTag_Intern_AnchorsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_queryFields = array('id');

    protected function _initColumns() {
        $this->_columns->add(new Kwf_Grid_Column('id', trlKwf('Anchor')));
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name')));
    }

    protected function _fetchData($order, $limit, $start)
    {
        $ret = array();
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('target'), array('ignoreVisible' => true));
        if ($component) {
            if (Kwc_Abstract::getFlag($component->componentClass, 'hasAnchors')) {
                foreach ($component->getComponent()->getAnchors() as $anchor => $name) {
                    $ret[] = array('id' => $anchor, 'name' => $name);
                }
            }

            foreach ($component->getRecursiveChildComponents(array('flag' => 'hasAnchors')) as $component) {
                foreach ($component->getComponent()->getAnchors() as $anchor => $name) {
                    $ret[] = array('id' => $anchor, 'name' => $name);
                }
            }
        }
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }
}
