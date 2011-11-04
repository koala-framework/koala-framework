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

    public function onPageChanged(Kwf_Component_Event_Page_Abstract $event)
    {
        $menuLevel = Kwc_Abstract::getSetting($this->_class, 'level');
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->dbId) as $data) {
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
                        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                            $this->_class, $m->dbId
                        ));
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
                    foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass($this->_class, $s) as $c) {
                        /*
                        if (true) { //TODO: more acurate
                            $this->fireEvent(new Kwf_Component_Event_Component_HasContentChanged(
                                $this->_class, $c->dbId
                            ));
                        }
                        */
                        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                            $this->_class, $c->dbId
                        ));
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
