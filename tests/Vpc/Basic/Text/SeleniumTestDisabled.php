<?php
class Vpc_Basic_Text_SeleniumTest extends PHPUnit_Framework_TestCase
{
}
/**
das funktioniert alles nicht; sowas zu testen ist mit selenium _etwas_ schwierig

http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000


 * @group slow
 * @group Vpc_Basic_Text
class Vpc_Basic_Text_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_Text_Root');
        parent::setUp();
    }

    public function testAdmin()
    {
        $this->openVpcEdit('Vpc_Basic_Text_TestComponent', 1000);
        $this->waitForConnections();
	

        $this->focus('dom=window.frames[0].document.body');
        $this->keyPress('dom=window.frames[0].document.body', 'a');
//         $this->assertEquals('afoo', $this->getText('dom=window.frames[0].document.body'));
//         $this->clickAt('dom=window.frames[0].document.body', '200,200');
        $this->assertElementPresent("//select[@class='x-font-select']");
        $this->assertElementValueEquals("//select[@class='x-font-select']", 'p');
        $this->select("//select[@class='x-font-select']", 'value=h2');
//         $this->assertElementValueEquals("//select[@class='x-font-select']", 'h2');

        for ($i=120;$i<256;$i++) {
            $this->keyDown('dom=window.frames[0].document.body', $i);
            $this->keyUp('dom=window.frames[0].document.body', $i);
            $this->keyPress('dom=window.frames[0].document.body', $i);
            $this->keyDown('dom=window.frames[0].document.body', "\\$i");
            $this->keyUp('dom=window.frames[0].document.body', "\\$i");
            $this->keyPress('dom=window.frames[0].document.body', "\\$i");
        }

//         $this->assertElementValueEquals("//select[@class='x-font-select']", 'p');

//         $this->keyPress('dom=window.frames[0].document.body', '\\38');
//         $this->assertElementValueEquals("//select[@class='x-font-select']", 'h2');

//         $this->click("//button[text()='".trlVps('Save')."']");
        sleep(500);
        //$this->assertEquals('foo', $this->getText('dom=window.frames[0].document.body'));

    }}
*/
