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
        $componentId = $this->_getParam('componentId') . '-linkTag-child';
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => true));
        if ($component && $component->getComponent() instanceof Kwc_Basic_LinkTag_Intern_Component) {
            $target = $component->getComponent()->getRow()->target;
            $component = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($target, array('ignoreVisible' => true));
            foreach ($component->getChildComponents(array('flag' => 'hasAnchors')) as $component) {
                foreach ($component->getComponent()->getAnchors() as $anchor => $name) {
                    $ret[] = array('id' => $anchor, 'name' => '#' . $name);
                }
            }
        }
        return $ret;
    }
}
