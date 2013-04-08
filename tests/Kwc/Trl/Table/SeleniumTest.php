<?php
/**
 * @group slow
 * @group selenium
 * @group Table
 */
class Kwc_Trl_Table_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Component_Data_Root::setComponentClass('Kwc_Trl_Table_Root_Root');
    }

    public function testAdmin()
    {
        $this->openKwcEdit('Kwc_Trl_Table_Table_Trl_Component.Kwc_Trl_Table_Table_Component', 'root-en_table');
        $this->waitForConnections();
    }
}
