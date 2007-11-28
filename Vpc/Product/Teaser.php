<?p
class Vpc_Product_Teaser extends Vpc_Abstra

    private $_productDat

    public function setProductData($dat
   
        $this->_productData = $dat
   
    public function getTemplateVars
   
        $ret = parent::getTemplateVars(
        $ret['product'] = $this->_productDat
        return $re
   
