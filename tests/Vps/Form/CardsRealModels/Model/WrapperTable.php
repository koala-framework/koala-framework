<?php
class Vps_Form_CardsRealModels_Model_WrapperTable extends Vps_Db_Table
{
    protected $_rowClass = 'Vps_Form_CardsRealModels_Model_WrapperTableRow';
    protected $_name = 'cards_wrapper';

   public function getServices()
   {
       return array('sibfirst'=>'Firstname',
                    'siblast'=>'Lastname');
   }
}
