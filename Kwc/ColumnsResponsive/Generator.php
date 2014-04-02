<?php
class Kwc_ColumnsResponsive_Generator extends Kwf_Component_Generator_Table
{
    protected function _fetchRows($parentData, $select)
    {
        if ($select->hasPart(Kwf_Component_Select::WHERE_ID)) {
            return array();
        }
        if (!$parentData) return array();
        $this->_getModel()->setData($parentData->componentClass, $parentData->dbId);
        return $this->_getModel()->getRows($select);
    }

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        if ($progressBar) $progressBar->next();
        $progressBar = null; //stop here, as getDuplicateProgressSteps doesn't go any deeper

        if ($source->generator !== $this) {
            throw new Kwf_Exception("you must call this only with the correct source");
        }

        //we don't need to duplicate the row, we just can re-use the same id as source had
        $id = $this->_idSeparator . $source->row->{$this->_getModel()->getPrimaryKey()};
        $target = array_pop($this->getChildData($parentTarget, array('id'=>$id, 'ignoreVisible'=>true, 'limit'=>1)));
        if (!$target) {
            return null;
        }
        Kwc_Admin::getInstance($source->componentClass)->duplicate($source, $target, $progressBar);
        return $target;
    }
}
