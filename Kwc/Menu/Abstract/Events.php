<?php
class Kwc_Menu_Abstract_Events extends Kwc_Abstract_Events
{
    protected $_numLevels = 1; //overridden in Menu_Expanded

    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Added',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_Removed',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_PositionChanged',
            'callback' => 'onPageChanged'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onParentChanged'
        );
        return $ret;
    }

    public function onParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }

    public function onPageChanged(Kwf_Component_Event_Page_Abstract $event)
    {
        $menuLevel = Kwc_Abstract::getSetting($this->_class, 'level');
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->dbId) as $data) {
            $level = 0;
            while ($data && !Kwc_Abstract::getFlag($data->componentClass, 'menuCategory')) {
                if ($data->isPage) $level++;
                $data = $data->parent;
            }
            $cat = Kwc_Abstract::getFlag($data->componentClass, 'menuCategory');
            if (is_int($menuLevel)) {
                if ($level+1 >= $menuLevel && $level+1 <= $menuLevel+$this->_numLevels) {
                    $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
                        $this->_class
                    ));
                }
            } else {
                if ($cat == $menuLevel && $level >= 1 && $level <= $this->_numLevels) {
                    $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
                        $this->_class
                    ));
                }
            }
        }
    }
}
