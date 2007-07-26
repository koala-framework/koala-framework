<?php
class Vpc_Rte_Setup extends Vpc_Setup_Abstract
{
	public function setup()
    {
		$fields['text'] = 'text NOT NULL';
		$this->createTable('component_rte', $fields);
    }

}