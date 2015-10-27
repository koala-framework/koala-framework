<?php
abstract class Kwc_Shop_AddToCartAbstract_OrderProductData
{
    protected $_class;

    public function __construct($componentClass)
    {
        $this->_class = $componentClass;
    }

    /**
     * @return $this
     */
    public static function getInstance($componentClass)
    {
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = Kwc_Abstract::getSetting($componentClass, 'orderProductData');
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }


    public function getAdditionalOrderData($row)
    {
        return array();
    }

    abstract public function getPrice($orderProduct);
    abstract public function getAmount($orderProduct);
    abstract public function getProductText($orderProduct);

    public function orderConfirmed($orderProduct)
    {
    }

    public function alterBackendOrderForm(Kwc_Shop_AddToCartAbstract_FrontendForm $form)
    {
    }

    /** This method is needed to support:
     *
     * multiple domain web where domains share products (getComponentsByDbId returns multiple, correct one is selected based on $subroot)
     * trl web where translated version of product has own db_id but lives in a different subroot (the $subroot won't be used in that case)
     */
    public static function getAddComponentByDbId($dbId, $subroot)
    {
        $ret = null;

        $addComponents = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId);
        if (count($addComponents) > 1) {
            foreach ($addComponents as $addComponent) {
                if ($addComponent->getSubroot() == $subroot->getSubroot()) {
                    $ret = $addComponent;
                    break;
                }
            }
        } else if (count($addComponents) == 1) {
            $ret = $addComponents[0];
        }

        return $ret;
    }
}
