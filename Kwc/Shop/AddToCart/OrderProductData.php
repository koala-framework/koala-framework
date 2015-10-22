<?php
class Kwc_Shop_AddToCart_OrderProductData extends Kwc_Shop_AddToCartAbstract_OrderProductData
{
    public function getPrice($orderProduct)
    {
        return $orderProduct->getParentRow('ProductPrice')->price * $orderProduct->amount;
    }

    public function getAmount($orderProduct)
    {
        return $orderProduct->amount;
    }

    public function getProductText($orderProduct)
    {
        $product = $orderProduct->getParentRow('ProductPrice')->getParentRow('Product');
        return $product->__toString();
    }

    public function getProductTextDynamic($orderProduct)
    {
        $ret = parent::getProductTextDynamic($orderProduct);
        $addCmp = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($orderProduct->add_component_id);
        if ($addCmp && is_instance_of($addCmp->parent->componentClass, 'Kwc_Shop_AddToCartAbstract_Trl_Component')) {
            $ret = $addCmp->getParentByClass('Kwc_Shop_Products_Detail_Trl_Component')->row->title;
        }
        return $ret;
    }

    public function getAdditionalOrderData($row)
    {
        $ret = parent::getAdditionalOrderData($row);
        $ret[] = array(
            'class' => 'amount',
            'name' => trlKwfStatic('Amount'),
            'value' => $row->amount
        );
        return $ret;
    }

    public function alterBackendOrderForm(Kwc_Shop_AddToCartAbstract_FrontendForm $form)
    {
        $component = null;
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Kwc_Shop_Products_Directory_Component')) {
                $detailClasses = Kwc_Abstract::getChildComponentClasses($c, 'detail');
                foreach ($detailClasses as $key=>$class) {
                    if (Kwc_Abstract::getChildComponentClass($class, 'addToCart') == $this->_class) {
                        $component = $key;
                    }
                }
            }
        }
        $m = Kwf_Model_Abstract::getInstance('Kwc_Shop_Products');
        $s = $m->select();
        $s->whereEquals('visible', 1);
        if ($component) $s->whereEquals('component', $component);
        $s->order('pos');
        $data = array();
        foreach ($m->getRows($s) as $product) {
            $data[] = array(
                $product->current_price_id,
                $product->__toString().' ('.$product->current_price.' €)'
            );
        }
        $form->prepend(new Kwf_Form_Field_Select('shop_product_price_id', trlKwfStatic('Product')))
            ->setValues($data)
            ->setAllowBlank(false);
    }
}
