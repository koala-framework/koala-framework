<?php
class Vpc_Basic_Text_Generator extends Vps_Component_Generator_Table
{
    protected function _getModel()
    {
        return Vpc_Basic_Text_Component::getTextModel($this->_class)
                ->getDependentModel('ChildComponents');
    }

    protected function _getIdFromRow($row)
    {
        return substr($row->component, 0, 1).$row->nr;
    }

    protected function _formatSelectId(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Model_Select::WHERE_ID)) {
            $id = $select->getPart(Vps_Model_Select::WHERE_ID);
            $select->unsetPart(Vps_Model_Select::WHERE_ID);
            if (substr($id, 0, 2)=='-l') {
                $select->whereEquals('component', 'link');
            } else if (substr($id, 0, 2)=='-d') {
                $select->whereEquals('component', 'download');
            } else if (substr($id, 0, 2)=='-i') {
                $select->whereEquals('component', 'image');
            } else {
                return null;
            }
            $select->whereEquals('nr', substr($id, 2));
        }
        return $select;
    }

    public function duplicateChild($source, $parentTarget)
    {
        if ($source->generator !== $this) {
            throw new Vps_Exception("you must call this only with the correct source");
        }
        $newRow = $source->row->duplicate(array(
            'component_id' => $parentTarget->dbId
        ));
        $id = '-' . substr($newRow->component, 0, 1) . $newRow->nr;
        $target = $parentTarget->getChildComponent($id);
        Vpc_Admin::getInstance($source->componentClass)->duplicate($source, $target);
        return $target;
    }
}
