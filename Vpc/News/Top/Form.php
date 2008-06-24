<?php
class Vpc_News_Top_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $news = Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass('Vpc_News_Directory_Component');

        $values = array();
        foreach ($news as $n) {
            $values[$n->dbId] =$n->getTitle();
        }

        $this->fields->add(new Vps_Form_Field_Select('news_component_id', trlVps('Show News')))
            ->setValues($values);
    }
}
