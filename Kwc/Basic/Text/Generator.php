<?php
class Kwc_Basic_Text_Generator extends Kwf_Component_Generator_Table
{
    protected function _getModel()
    {
        return Kwc_Basic_Text_Component::getTextModel($this->_class)
                ->getDependentModel('ChildComponents');
    }

    protected function _getIdFromRow($row)
    {
        return substr($row->component, 0, 1).$row->nr;
    }

    protected function _formatSelectId(Kwf_Component_Select $select)
    {
        if ($select->hasPart(Kwf_Model_Select::WHERE_ID)) {
            $id = $select->getPart(Kwf_Model_Select::WHERE_ID);
            $select->unsetPart(Kwf_Model_Select::WHERE_ID);
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
            throw new Kwf_Exception("you must call this only with the correct source");
        }
        $newRow = $source->row->duplicate(array(
            'component_id' => $parentTarget->dbId
        ));
        $id = '-' . substr($newRow->component, 0, 1) . $newRow->nr;
        $target = $parentTarget->getChildComponent($id);
        Kwc_Admin::getInstance($source->componentClass)->duplicate($source, $target);
        return $target;
    }
}
