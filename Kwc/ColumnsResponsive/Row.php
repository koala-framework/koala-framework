<?php
class Kwc_ColumnsResponsive_Row extends Kwf_Model_Row_Data_Abstract
{
    // this is an fnfRow
    // when the component duplicates the fnfRow has return its own value with the correct new componentId
    // and not create an new row
    public function duplicate(array $data = array())
    {
        $data = array_merge($this->toArray(), $data);
        return new Kwc_ColumnsResponsive_Row(array('data' => $data, 'model' => $this->getModel()));
    }
}
