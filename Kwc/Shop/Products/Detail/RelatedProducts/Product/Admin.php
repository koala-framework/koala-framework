<?php
class Kwc_Shop_Products_Detail_RelatedProducts_Product_Admin extends Kwc_Abstract_Composite_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Shop_Products');
        return $model->getRow($data->getComponent()->getRow()->product_id)->__toString();
    }

    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('string', trlKwf('Product'), 200);
        $c->setData(new Kwf_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        return $ret;
    }
}
