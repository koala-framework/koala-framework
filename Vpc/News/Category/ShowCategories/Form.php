<?php
class Vpc_News_Category_ShowCategories_Form extends Vps_Form_NonTableForm
{
    public function __construct($name, $class)
    {
        $this->setProperty('class', $class);
        parent::__construct($name);

        $showNewsClass = Vpc_Abstract::getSetting($class, 'showNewsClass');
        
        $tc = new Vps_Dao_TreeCache();
        $p = new Vps_Dao_Pool();
        $news = $tc->findComponentsByParentClass('Vpc_News_Category_Directory_Component');

        $values = array();
        foreach ($news as $new) {
            $pool = Vpc_Abstract::getSetting($new->component_class, 'pool');
            $new = $new->getComponent()->getNewsComponent();
            if ($new->component_class == $showNewsClass || is_subclass_of($new->component_class, $showNewsClass)) {
                foreach ($p->fetchAll(array('pool = ?' => $pool)) as $cat) {
                    $values[$new->component_id.'#'.$cat->id] = $new->getTitle().' - '.$cat->__toString();
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
