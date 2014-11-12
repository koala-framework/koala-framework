<?php
/**
 * @group slow
 * @group selenium
 * @group Vpc_Basic_Text
 */
class Vpc_Basic_TextSessionModel_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Basic_TextSessionModel_Root');
        parent::setUp();
    }

    public function testInsertEditLink()
    {
        $this->openVpcEdit('Vpc_Basic_TextSessionModel_TestComponent', 'root_text');
        $this->waitForConnections();

        //insert some text
        $this->typeKeys("document.getElementsByTagName('iframe')[0].contentDocument.body", 'lalalulu');

        //assert createlink action is disabled
        $this->runScript("window.action = null; Ext.ComponentMgr.all.each(function(c) { if (c.initialConfig.testId == 'createlink') { action = c; return false; } });");
        $this->assertEquals('true', $this->getEval('window.action.disabled'));

        //select some text
        $this->runScript('var editor = null;
            Ext.ComponentMgr.all.each(function(c) { if (c instanceof Vps.Form.HtmlEditor) { editor = c; return false; } });
            var rng = editor.tinymceEditor.selection.getRng();
            rng.setStart(rng.startContainer, 4);
            editor.tinymceEditor.selection.setRng(rng);
            editor.onEditorEvent();
        ');

        //assert createlink action is now enabled
        $this->assertEquals('false', $this->getEval('window.action.disabled'));

        //open create link dialog
        $this->mouseDown("dom=window.action.el.child('button').dom");
        $this->mouseUp("dom=window.action.el.child('button').dom");
        $this->waitForConnections();

        //select extern link
        $this->runScript("Ext.ComponentMgr.all.each(function(c) {
            if (c instanceof Vps.Form.ComboBox && c.name == 'component') {
                c.setValue('extern');
                c.fireEvent('select');
            }
        });
        ");
        $this->type('css=input[name="extern_target"]', 'http://orf.at');
        $this->click("css=.x-window button:contains('Save')");
        $this->waitForConnections();

        //assert inserted a tag
        $this->selectFrame('//iframe');
        $this->assertAttribute('//p/a/@href', 'root_text-l1');
        $this->selectFrame('relative=top');

        //save everything
        $this->click("css=button:contains('Save')");
        $this->waitForConnections();

        //assert frontent content
        $this->openVpc('/text');
        $this->assertText('css=.vpcText', 'lalalulufoo');
        $this->assertAttribute('//div/p/a/@href', 'http://orf.at');

        //open backend again
        $this->openVpcEdit('Vpc_Basic_TextSessionModel_TestComponent', 'root_text');
        $this->waitForConnections();

        //select link
        $this->runScript('var editor = null;
            Ext.ComponentMgr.all.each(function(c) { if (c instanceof Vps.Form.HtmlEditor) { editor = c; return false; } });
            editor.onEditorEvent();
            var a = Ext.fly(editor.getDoc()).child("a").dom;
            editor.tinymceEditor.selection.select(a);
            editor.onEditorEvent();
        ');

        //assert createlink action is enabled
        $this->runScript("window.action = null; Ext.ComponentMgr.all.each(function(c) { if (c.initialConfig.testId == 'createlink') { action = c; return false; } });");
        $this->assertEquals('false', $this->getEval('window.action.disabled'));

        //open create link dialog
        $this->mouseDown("dom=window.action.el.child('button').dom");
        $this->mouseUp("dom=window.action.el.child('button').dom");
        $this->waitForConnections();

        //assert previously isnerted text
        $this->assertVisible('css=input[name="extern_target"]');
        $this->assertElementValueEquals('css=input[name="extern_target"]', 'http://orf.at');
        
        sleep(5);
    }

    public function testInlineStyle()
    {
        $this->openVpcEdit('Vpc_Basic_TextSessionModel_TestComponent', 'root_text');
        $this->waitForConnections();

        //insert some text
        $this->typeKeys("document.getElementsByTagName('iframe')[0].contentDocument.body", 'bar baz');

        //select some text
        $this->runScript('var editor = null;
            Ext.ComponentMgr.all.each(function(c) { if (c instanceof Vps.Form.HtmlEditor) { editor = c; return false; } });
            var rng = editor.tinymceEditor.selection.getRng();
            rng.setStart(rng.startContainer, 4);
            editor.tinymceEditor.selection.setRng(rng);
            editor.onEditorEvent();
        ');

        //choose style
        $this->runScript('
            Ext.ComponentMgr.all.each(function(c) {
                if (c.testId=="inlineStyleSelect") {
                    c.setValue("style3");
                    c.fireEvent("select", c);
                    return false;
                }
            });
        ');

        //assert inserted a tag
        $this->selectFrame('//iframe');
        $this->assertAttribute('//p/span/@class', 'style3');
        $this->selectFrame('relative=top');

        //choose style
        $this->runScript('
            Ext.ComponentMgr.all.each(function(c) {
                if (c.testId=="inlineStyleSelect") {
                    c.setValue("inlinedefault");
                    c.fireEvent("select", c);
                    return false;
                }
            });
        ');

        //assert inserted a tag
        $this->selectFrame('//iframe');
        $this->assertElementNotPresent('//p/span/@class');
        $this->selectFrame('relative=top');
    }

    public function testBlockStyle()
    {
        $this->openVpcEdit('Vpc_Basic_TextSessionModel_TestComponent', 'root_text');
        $this->waitForConnections();

        //insert some text
        $this->typeKeys("document.getElementsByTagName('iframe')[0].contentDocument.body", 'bar baz');
        usleep(100*1000);

        //choose style
        $this->runScript('
            Ext.ComponentMgr.all.each(function(c) {
                if (c.testId=="blockStyleSelect") {
                    c.setValue("style1");
                    c.fireEvent("select", c);
                    return false;
                }
            });
        ');

        //assert inserted tag
        $this->selectFrame('//iframe');
        $this->assertAttribute('//h1/@class', 'style1');
        $this->selectFrame('relative=top');

        //choose style
        $this->runScript('
            Ext.ComponentMgr.all.each(function(c) {
                if (c.testId=="blockStyleSelect") {
                    c.setValue("blockdefault");
                    c.fireEvent("select", c);
                    return false;
                }
            });
        ');

        //assert inserted tag
        $this->selectFrame('//iframe');
        $this->assertElementNotPresent('//h1');
        $this->assertElementPresent('//p');
        $this->selectFrame('relative=top');
    }

    public function testBlockStyleOnTwoParagraphs()
    {
        $this->openVpcEdit('Vpc_Basic_TextSessionModel_TestComponent', 'root_text');
        $this->waitForConnections();

        //insert some text
        $this->typeKeys("document.getElementsByTagName('iframe')[0].contentDocument.body", "bar baz\n");
        $this->keyPress("document.getElementsByTagName('iframe')[0].contentDocument.body", 13);

        $this->selectFrame('//iframe');
        $this->assertXpathCount('//body/p', 2);
        $this->selectFrame('relative=top');

        //select some text
        $this->runScript('var editor = null;
            Ext.ComponentMgr.all.each(function(c) { if (c instanceof Vps.Form.HtmlEditor) { editor = c; return false; } });
            var bookmark = {
                start: [0,0,0],
                end: [3,0,1]
            };
            editor.tinymceEditor.selection.moveToBookmark(bookmark);
            editor.onEditorEvent();
        ');

        //choose style
        $this->runScript('
            window.blockStyleSelect = null;
            Ext.ComponentMgr.all.each(function(c) {
                if (c.testId=="blockStyleSelect") {
                    window.blockStyleSelect = c;
                    c.setValue("style1");
                    c.fireEvent("select", c);
                    return false;
                }
            });
        ');

        $this->selectFrame('//iframe');
        $this->assertXpathCount('//body/p', 0);
        $this->assertXpathCount('//body/h1', 2);
        $this->selectFrame('relative=top');

        $this->runScript('editor.onEditorEvent();');

        //assert selected style
        $this->assertEquals('style1', $this->getEval('window.blockStyleSelect.getValue()'));

    }
}
