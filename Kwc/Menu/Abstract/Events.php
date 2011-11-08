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
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onRecursiveRemoved'
        );
        return $ret;
    }

    public function onParentChanged(Kwf_Component_Event_Page_ParentChanged $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }

    //overridden in Kwc_Menu_Events to fire HasContentChanged
    protected function _onMenuChanged(Kwf_Component_Event_Component_Abstract $event, Kwf_Component_Data $menu)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $menu->dbId
        ));
    }

    public function onPageChanged(Kwf_Component_Event_Component_Abstract $event)
    {
        $menuLevel = Kwc_Abstract::getSetting($this->_class, 'level');
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->dbId, array('ignoreVisible'=>true)) as $data) {
            $level = 0;
            $categoryData = $data;
            while ($categoryData && !Kwc_Abstract::getFlag($categoryData->componentClass, 'menuCategory')) {
                if ($categoryData->isPage) $level++;
                $categoryData = $categoryData->parent;
            }
            if (is_int($menuLevel)) {
                if ($level+1 >= $menuLevel && $level+1 <= $menuLevel+$this->_numLevels) {
                    $l = $level + 1;
                    while ($data) {
                        if ($l == $menuLevel) {
                            break;
                        }
                        if ($data->isPage) $l--;
                        $data = $data->parent;
                    }
                    $data = $data->getPageOrRoot();
                    $menus = $data->getRecursiveChildComponents(array('componentClass'=>$this->_class));
                    foreach ($menus as $m) {
                        $this->_onMenuChanged($event, $m);
                    }
                }
            } else {
                $cat = Kwc_Abstract::getFlag($categoryData->componentClass, 'menuCategory');
                if ($cat) {
                    if ($cat === true) $cat = $categoryData->id;
                }
                if ($cat === $menuLevel && $level >= 1 && $level <= $this->_numLevels) {
                    $s = array(
                        'subroot' => $data
                    );
                    foreach (Kwc_Abstract::getComponentClasses() as $cls) {
                        $c = Kwc_Abstract::getFlag($cls, 'menuCategory');
                        $cmps = array();
                        if ($c === true) {
                            $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByClass($cls, array('id'=>$cat));
                        } else if ($c == $cat) {
                            $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByClass($cls);
                        }
                        foreach ($cmps as $i) {
                            foreach ($i->getRecursiveChildComponents(array('componentClass'=>$this->_class)) as $m) {
                                $this->_onMenuChanged($event, $m);
                            }
                        }
                    }
                }
            }
        }
    }

    public function onRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        //TODO: Component_RecursiveContentChanged could be used, ParentMenu etc have to listen to that too then
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged(
            $this->_class
        ));
    }
}
