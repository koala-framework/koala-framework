<?php
class Kwc_Menu_Abstract_Events extends Kwc_Abstract_Events
{
    protected $_numLevels = 1; //overridden in Menu_Expanded

    protected $_level; //level setting of menu

    protected function _init()
    {
        parent::_init();
        $this->_initSettings();
    }

    //overwritten in Kwc_Menu_Trl_Events
    protected function _initSettings()
    {
        $this->_level = $menuLevel = Kwc_Abstract::getSetting($this->_class, 'level');
    }

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
            'event' => 'Kwf_Component_Event_Page_ShowInMenuChanged',
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
            $this->_class, $event->component
        ));
    }

    //overridden in Kwc_Menu_Events to fire HasContentChanged
    protected function _onMenuChanged(Kwf_Component_Event_Component_Abstract $event, Kwf_Component_Data $menu)
    {
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
            $this->_class, $menu
        ));
    }

    public function onPageChanged(Kwf_Component_Event_Component_Abstract $event)
    {
        $menuLevel = $this->_level;

        $data = $event->component;

        if (!$event instanceof Kwf_Component_Event_Page_ShowInMenuChanged
            && !$data->isShownInMenu()
        ) {
            //ignore pages not shown in menu
            return;
        }

        //find category + level the changed page is in
        $level = 0;
        $categoryData = $data;

        while ($categoryData && !Kwc_Abstract::getFlag($categoryData->componentClass, 'menuCategory')) {
            if ($categoryData->isPage) $level++;
            $categoryData = $categoryData->parent;
        }

        if (is_int($menuLevel)) {
            //numeric menu level
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
            //category menu level

            if (!$categoryData) return;

            //$cat is the category id of changed page
            $cat = Kwc_Abstract::getFlag($categoryData->componentClass, 'menuCategory');
            if ($cat) {
                if ($cat === true) $cat = $categoryData->id;
            }

            if ($cat === $menuLevel && $level >= 1 && $level <= $this->_numLevels) {
                //this menu shows this changed and not in a lower level
                $s = array(
                    'subroot' => $data
                );
                foreach (Kwc_Abstract::getComponentClasses() as $cls) { //get all categories
                    $c = Kwc_Abstract::getFlag($cls, 'menuCategory');
                    $cmps = array();
                    //get category that changed
                    if ($c === true) {
                        $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByClass($cls, array('id'=>$cat, 'subRoot'=>$data));
                    } else if ($c == $cat) {
                        $cmps = Kwf_Component_Data_Root::getInstance()->getComponentsByClass($cls, array('subRoot'=>$data));
                    } else {
                        continue;
                    }
                    foreach ($cmps as $i) {
                        //get menu that changed
                        foreach ($i->getRecursiveChildComponents(array('componentClass'=>$this->_class)) as $m) {
                            //do what needs to be done if menu item changed
                            $this->_onMenuChanged($event, $m);
                        }
                    }
                }
            }
        }
    }

    public function onRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        $components = $event->component->getRecursiveChildComponents(
            array('showInMenu' => true, 'page' => true)
        );
        foreach ($components as $component) {
            $this->onPageChanged(new Kwf_Component_Event_Page_Removed(
                $component->componentClass, $component, Kwf_Component_Event_Page_Removed::FLAG_ROW_ADDED_REMOVED
            ));
        }
    }
}
