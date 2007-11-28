<?p
/
 * Beispieldecorat

 * @package V
 * @subpackage Decorat
 
class Vpc_Decorator_Simple_Color_Component extends Vpc_Decorator_Abstra

    protected $_decorate

    public function getTemplateVars
   
        $ret = parent::getTemplateVars(
        $ret['decorated'] = $this->_component->getTemplateVars(
        $ret['color'] = 'blue
        $ret['template'] = 'Decorator.html
        return $re
   
  

