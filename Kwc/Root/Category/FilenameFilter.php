<?php
class Kwc_Root_Category_FilenameFilter extends Kwf_Filter_Row_Abstract
{
    public function skipFilter($row, $column)
    {
        if ($row->custom_filename) return true;
        return parent::skipFilter($row, $column);
    }

    public function filter($row)
    {
        $value = Kwf_Filter::filterStatic($row->name, 'Ascii');

        $componentId = $this->_getComponentId($row);

        $parent = $this->_getParentPage($row);

        $values = array();
        foreach ($parent->getChildPages(array('ignoreVisible' => true)) as $c) {
            if ($c->componentId == $componentId) continue;
            $values[] = $c->filename;
        }
        if ($parent instanceof Kwf_Component_Data_Root) {
            $values[] = 'admin';
            $values[] = 'kwf';
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

    // trl has no parent_id
    protected function _getParentPage($row)
    {
        return Kwf_Component_Data_Root::getInstance()
            ->getComponentById($row->parent_id, array('ignoreVisible' => true))
            ->getPseudoPageOrRoot();
    }

    protected function _getComponentId($row)
    {
        return $row->id;
    }
}
