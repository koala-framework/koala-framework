<?php
class Vpc_News_Top_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $tc = new Vps_Dao_TreeCache();
        $news = $tc->findComponentsByParentClass('Vpc_News_Directory_Component');

        $values = array();
        foreach ($news as $n) {
            $values[$n->db_id] =$n->getTitle();
        }

        $this->fields->add(new Vps_Form_Field_Select('news_component_id', trlVps('Show News')))
            ->setValues($values);
    }
}
