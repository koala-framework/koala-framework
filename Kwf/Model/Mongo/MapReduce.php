<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Mongo_MapReduce extends Kwf_Model_Mongo
{
    protected $_primaryKey = '_id';
    protected $_rowClass = 'Kwf_Model_Mongo_MapReduce_Row';

    public function update() {}

    //TODO: bei whereEquals, sort usw value. autom dazugeben
}
