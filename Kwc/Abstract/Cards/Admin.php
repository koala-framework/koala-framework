<?php
class Kwc_Abstract_Cards_Admin extends Kwc_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $child = $data->getChildComponent(array(
            'generator' => 'child'
        ));
        return Kwc_Admin::getInstance($child->componentClass)->componentToString($child);
    }
}
