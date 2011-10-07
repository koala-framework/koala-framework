<?php
class Kwf_Model_Proxycache_TestFnF extends Kwf_Model_FnF
{
     public $getRowsCalled = 0;
     public function getRows($where=null, $order=null, $limit=null, $start=null)
     {
         $this->getRowsCalled++;
         return parent::getRows($where, $order, $limit, $start);
     }
}
