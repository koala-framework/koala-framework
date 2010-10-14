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

*** Fett setzen ***
- url aufrufen http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- 'foo bar baz' in rte schreiben
- bar markieren
- 'B' klicken
- Erwartet: bar wird fett
- in HTML-Code-Ansicht umschalten
- Erwartet: <strong>bar</strong>
- zurückschalten
- Erwartet: bar ist immer noch fett

*** Fett zurücksetzen ***
- url aufrufen http://vps.vps.niko.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- 'foo bar baz' in rte schreiben
- bar markieren
- 'B' klicken
- Erwartet: bar wird fett
- 'B' klicken
- Erwartet: bar ist nicht mehr fett
- 'B' klicken
- Erwartet: bar wird fett
- in HTML-Code-Ansicht umschalten
- Erwartet: <strong>bar</strong>
- zurückschalten
- Erwartet: bar ist immer noch fett
- bar markieren
- 'B' klicken
- Erwartet: bar ist nicht mehr fett

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

*** Cursor nach einfügen ganz am Anfang ***
- url aufrufen: http://fnprofile.markus.vivid/vps/componentedittest/Vpc_Basic_Text_Root/Vpc_Basic_Text_TestComponent/Index?componentId=1000
- 'ein text' in die Zwischenablage
- nach 'f' in 'foo' in den rte klicken und Strg+V drücken
- Erwartet: cursor ist nach 'ein text' und vor 'oo'

*** [TODO] Fett geht nicht mehr weg ***
- mehrere zeilen tippen und in liste umwandeln
- ein paar punkte makieren und fett machen
- ein wort markieren und fett wegnehmen
- speichern und zurück
- absatz wieder bearbeiten: es ist wieder alles fett, auch bei dem weggenommenen
- wenn man was markiert und fett wegnehmen will ist das nicht mehr möglich

*** [TODO] Fett geht nicht her ***
- neuer text bild absatz
- lorem ipsum markieren und auf sauberen text einfügen klicken
- eine zeile (zwei wörter oder so) einfügen
- text markieren und auf fett klicken
- speichern und zurück
- es ist nicht fett...
- (hinweis): Wenn man vor dem fett-klick einmal auf html-ansicht und zurück schaltet funktionierts schon

*** [TODO] Features / Bugs von niko
- inline styles nur nur für "span" sondern auch für "a"
- zwei <p>s markieren, Überschrift 1 auswählen,
  Grundeinstellung wird angezeigt, Überschrift 1 sollte aber
- bold ged ned immer:
   ul mit 3 einträgen, mittlere von ganz links weg nach ganz
   rechts markieren, bold machen, tidyn -> bold is weg:
  <li style="font-weight: bold;">Manuelle Testläufe  </li>
- IE7+
- brotkrümel für dom struktur von cursor


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
