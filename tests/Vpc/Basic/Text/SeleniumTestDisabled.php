<?php
class Vpc_Basic_Text_SeleniumTest extends PHPUnit_Framework_TestCase
{
}
/**
das funktioniert alles nicht; sowas zu testen ist mit selenium _etwas_ schwierig

http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000

=== MANUELLE TESTS ===

*** Enter erzeugt Absatz ***
- url aufrufen http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- hinter foo klicken
- Enter drücken
- bar schreiben
- Shift+Enter drücken
- baz schreiben
- in HTML-Code-Ansicht umschalten
- Erwarteter HTML-Code: <p>foo</p><p>bar<br />baz</p>

*** Inline Style setzen ***
- url aufrufen http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- 'foo bar baz' in rte schreiben
- bar markieren
- Inline: Test3 auswählen
- Erwartet: bar wird grün und kleiner
- Bonus Markierung bleibt erhalten (im moment nur FF)

*** Inline Style ändern ***
- url aufrufen http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- 'foo bar baz' in rte schreiben
- bar markieren
- Inline: Test3 auswählen
- Erwartet: bar wird grün und kleiner
- IE: bar markieren / FF: cursor in bar setzen (TODO: bei beiden soll beides gehen)
- Inline: Default auswählen
- Erwartet: bar wird wida schwarz

*** Cursor beim einfügen ***
- url aufrufen: http://fnprofile.markus.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- 'Text' in Zwischenablage kopieren
- in RTE Strg+V drücken
- Erwartet: Cursor muss blinkend sichtbar sein

*** Markierter text beim einfügen entfernen ***
- url aufrufen: http://fnprofile.markus.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- 'Ich mag Kekse!' in rte schreiben
- 'Hunde' in Zwischenablage kopieren
- 'Kekse' in RTE markieren
- Strg+V drücken
- Erwarteter Text in RTE: 'Ich mag Hunde!'

*** Interner-Link einfügen nicht möglich ***
- url aufrufen: http://fnprofile.markus.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- foo markieren
- auf link einfügen in toolbar klicken
- einen internen link in seitenbaum auswählen
- alles bestätigen bis kein fenster mehr offen ist
- Erwartet: foo ist als link hinterlegt

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
