<?php
class Vpc_Posts_Post_Quote_Form_Form extends Vpc_Posts_Write_Form_Form
{
    public function load($parentRow, $postData = array())
    {
        $ret = parent::load($parentRow, $postData);
        $ret['Vpc_Posts_Post_Quote_Form_Component_content'] = 
            "[quote]\n" . $this->getQuoteText() . "\n[/quote]\n";
        return $ret;
    }
}
