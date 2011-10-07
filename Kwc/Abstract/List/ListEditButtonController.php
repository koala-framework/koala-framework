<?php
class Kwc_Abstract_List_ListEditButtonController extends Kwc_Abstract_List_Controller
{
    protected $_position = 'pos';
    protected function _initColumns()
    {
        parent::_initColumns();

        $extConfig = Kwc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $extConfig = $extConfig['list'];
        $i=0;
        foreach ($extConfig['contentEditComponents'] as $ec) {
            if (isset($ec['title'])) {
                $name = $ec['title'];
                $icon = $ec['icon'];
            } else {
                $name = Kwc_Abstract::getSetting($ec['componentClass'], 'componentName');
                $icon = Kwc_Abstract::getSetting($ec['componentClass'], 'componentIcon');
                $icon = $icon->toString(array('arrow'));
            }
            $this->_columns->add(new Kwf_Grid_Column_Button('edit_'.$i, ' ', 20))
                ->setNoIconWhenNew(true)
                ->setColumnType('editContent')
                ->setEditComponentClass($ec['componentClass'])
                ->setEditType($ec['type'])
                ->setEditIdTemplate($ec['idTemplate'])
                ->setEditComponentIdSuffix($ec['componentIdSuffix'])
                ->setButtonIcon($icon)
                ->setTooltip(trlKwf('Edit {0}', $name));
            $i++;
        }

    }
}
