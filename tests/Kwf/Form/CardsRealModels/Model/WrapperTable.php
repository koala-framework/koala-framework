<?php
class Kwf_Form_CardsRealModels_Model_WrapperTable extends Kwf_Db_Table
{
    protected $_rowClass = 'Kwf_Form_CardsRealModels_Model_WrapperTableRow';
    protected $_name = 'cards_wrapper';

   public function getServices()
   {
       return array('sibfirst'=>'Firstname',
                    'siblast'=>'Lastname');
   }
}
