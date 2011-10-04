<?php
class Vps_Model_Mongo_MapReduce extends Vps_Model_Mongo
{
    protected $_primaryKey = '_id';
    protected $_rowClass = 'Vps_Model_Mongo_MapReduce_Row';

    public function update() {}

    //TODO: bei whereEquals, sort usw value. autom dazugeben
}
