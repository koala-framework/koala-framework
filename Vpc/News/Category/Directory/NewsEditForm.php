<?php
class Vpc_News_Category_Directory_NewsEditForm extends Vps_Form_NonTableForm
{
    public function __construct($class, $id = null)
    {
        parent::__construct($class, $id);

        $this->add(new Vps_Form_Field_PoolMulticheckbox('Vpc_News_Category_Directory_NewsToCategoriesModel', trlVps('Categories')))
             ->setPool(Vpc_Abstract::getSetting($class, 'pool'))
             ->setReferences(array(
                'columns' => array('news_id'),
                'refColumns' => array('id')
            ));
    }
}
