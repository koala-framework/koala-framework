<?php
class Kwc_Directories_AjaxView_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Directories_AjaxView_Root');
    }
    public function testIt()
    {
        echo "--\n";
        $s = new Kwf_Model_Select();

        $s2 = new Kwf_Model_Select();
        $s2->whereEquals('category_id', 1);
        $s->where(new Kwf_Model_Select_Expr_Child_Contains('Categories', $s2));

        d(Kwf_Model_Abstract::getInstance('Kwc_Directories_AjaxView_Directory_Model')->getRows($s)->toArray());
        echo "==\n";
    }
}
