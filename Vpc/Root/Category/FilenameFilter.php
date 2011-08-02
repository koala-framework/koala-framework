<?php
class Vpc_Root_Category_FilenameFilter extends Vps_Filter_Row_Abstract
{
    public function skipFilter($row, $column)
    {
        if ($row->custom_filename) return true;
        return parent::skipFilter($row, $column);
    }

    public function filter($row)
    {
        $value = Vps_Filter::filterStatic($row->name, 'Ascii');

        $componentId = $this->_getComponentId($row);
        if (!$componentId && isset($row->parent_id)) {
            $parent = Vps_Component_Data_Root::getInstance()
                ->getComponentById($row->parent_id, array('ignoreVisible' => true));
        } else {
            $parent = Vps_Component_Data_Root::getInstance()
                ->getComponentById($componentId, array('ignoreVisible' => true))
                ->parent;
        }
        $parent = $parent->getPseudoPageOrRoot();

        $values = array();
        foreach ($parent->getChildPages(array('ignoreVisible' => true)) as $c) {
            if ($c->componentId == $componentId) continue;
            $values[] = $c->filename;
        }
        if ($parent instanceof Vps_Component_Data_Root) {
            $values[] = 'admin';
            $values[] = 'vps';
            $values[] = 'media';
            $values[] = 'assets';
        }

        $x = 0;
        $unique = $value;
        if (!$unique) $unique = 1;
        while (in_array($unique, $values)) {
            if ($value) {
                $unique = $value . '_' . ++$x;
            } else {
                $unique = ++$x;
            }
        }
        return (string)$unique;
    }

    protected function _getComponentId($row)
    {
        return $row->id;
    }
}
