<?php
class Vpc_News_Category_ShowCategories_Form extends Vps_Form_NonTableForm
{
    public function __construct($name, $class)
    {
        $this->setProperty('class', $class);
        parent::__construct($name);

        $showNewsClass = Vpc_Abstract::getSetting($class, 'showNewsClass');
        
        $p = new Vps_Dao_Pool();
        $news = Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass('Vpc_News_Category_Directory_Component');

        $values = array();
        foreach ($news as $new) {
            $pool = Vpc_Abstract::getSetting($new->componentClass, 'pool');
            $new = $new->getComponent()->getNewsComponent();
            if ($new->componentClass == $showNewsClass || is_subclass_of($new->componentClass, $showNewsClass)) {
                foreach ($p->fetchAll(array('pool = ?' => $pool)) as $cat) {
                    $values[$new->dbId.'#'.$cat->id] = $new->getTitle().' - '.$cat->__toString();
                }
            }
        }
        $this->add(new Vps_Form_Field_MultiCheckbox('Vpc_News_Category_ShowCategories_Model', trlVps('Categories')))
            ->setValues($values)
            ->setReferences(array(
                'columns' => array('component_id'),
                'refColumns' => array('id')
            ))
            ->setColumnName('category');
    }
}
