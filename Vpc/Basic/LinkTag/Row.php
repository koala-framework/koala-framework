<?php
class Vpc_Basic_LinkTag_Row extends Vpc_Row
{
    protected function _update()
    {
        if ($this->_cleanData['link_class'] != $this->link_class) {
            Vpc_Admin::getInstance($this->_cleanData['link_class'])
                        ->delete($this->component_id.'-1');
        }
    }

    protected function _delete()
    {
        Vpc_Admin::getInstance($this->link_class)
                    ->delete($this->component_id.'-1');
    }
}
