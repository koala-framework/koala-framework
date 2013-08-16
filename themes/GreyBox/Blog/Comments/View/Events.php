<?php
class GreyBox_Blog_Comments_View_Events extends Kwc_Directories_List_View_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach ($this->_getCreatingClasses($this->_class) as $i) {
            foreach ($this->_getCreatingClasses($i) as $j) {
                $ret[] = array(
                    'class' => $j,
                    'event' => 'Kwf_Component_Event_Component_ContentChanged',
                    'callback' => 'onDetailContentChanged'
                );
            }
        }
        return $ret;
    }

    public function onDetailContentChanged(Kwf_Component_Event_Component_ContentChanged $ev)
    {
        $view = $ev->component->getRecursiveChildComponent(array('componentClass'=>$this->_class));
        $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $view));
    }
}
