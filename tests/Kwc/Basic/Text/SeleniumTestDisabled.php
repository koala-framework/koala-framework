<?php
class Kwc_Basic_Text_SeleniumTest extends Kwc_TestAbstract
{
}
/**
das funktioniert alles nicht; sowas zu testen ist mit selenium _etwas_ schwierig

http://kwf.kwf.niko.vivid/kwf/componentedittest/Kwc_Basic_Text_Root/Kwc_Basic_Text_TestComponent/Index?componentId=1000


 * @group slow
 * @group Kwc_Basic_Text
class Kwc_Basic_Text_SeleniumTest extends Kwf_Test_SeleniumTestCase
{
    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass('Kwc_Basic_Text_Root');
        parent::setUp();
    }

    public function testAdmin()
    {
        $this->openKwcEdit('Kwc_Basic_Text_TestComponent', 1000);
        $this->waitForConnections();


        $this->focus('dom=window.frames[0].document.body');
        $this->keyPress('dom=window.frames[0].document.body', 'a');
//         $this->assertEquals('afoo', $this->getText('dom=window.frames[0].document.body'));
//         $this->clickAt('dom=window.frames[0].document.body', '200,200');
        $this->assertElementPresent("//select[@class='x2-font-select']");
        $this->assertElementValueEquals("//select[@class='x2-font-select']", 'p');
        $this->select("//select[@class='x2-font-select']", 'value=h2');
//         $this->assertElementValueEquals("//select[@class='x2-font-select']", 'h2');

        for ($i=120;$i<256;$i++) {
            $this->keyDown('dom=window.frames[0].document.body', $i);
            $this->keyUp('dom=window.frames[0].document.body', $i);
            $this->keyPress('dom=window.frames[0].document.body', $i);
            $this->keyDown('dom=window.frames[0].document.body', "\\$i");
            $this->keyUp('dom=window.frames[0].document.body', "\\$i");
            $this->keyPress('dom=window.frames[0].document.body', "\\$i");
        }

//         $this->assertElementValueEquals("//select[@class='x2-font-select']", 'p');

//         $this->keyPress('dom=window.frames[0].document.body', '\\38');
//         $this->assertElementValueEquals("//select[@class='x2-font-select']", 'h2');

//         $this->click("//button[text()='".trlKwf('Save')."']");
        sleep(500);
        //$this->assertEquals('foo', $this->getText('dom=window.frames[0].document.body'));

    }}
*/
