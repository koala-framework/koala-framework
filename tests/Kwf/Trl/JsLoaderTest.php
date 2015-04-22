<?php
/**
 * @group jstrl
 * @group trl
 *
 */
class Kwf_Trl_JsLoaderTest extends Kwf_Test_TestCase
{
    private $_jsLoader;
    public function setUp()
    {
        parent::setUp();
        $this->_jsLoader = new Kwf_Trl_JsLoader();
    }

    public function testTrl()
    {
        $input = "trl('undefined word')";
        $expected = "trl('undefined word')";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);

        $input = "trl(\"undefined word\")";
        $expected = "trl(\"undefined word\")";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);
    }

    public function testTrlc()
    {
        $input = "trlc('anycontext', 'undefined word')";
        $expected = "trl('undefined word')";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);

        $input = "trlc(\"anycontext\", \"undefined word\")";
        $expected = "trl(\"undefined word\")";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);
    }

    public function testTrlp()
    {
        $input = "trlp('undefined word', 'undefined words', 2)";
        $expected = "trlp('undefined word', 'undefined words', 2)";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);

        $input = "trlp(\"undefined word\", \"undefined words\", 2)";
        $expected = "trlp(\"undefined word\", \"undefined words\", 2)";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);
    }

    public function testTrlcp()
    {
        $input = "trlcp('anycontext', 'undefined word', 'undefined words', 2)";
        $expected = "trlp('undefined word', 'undefined words', 2)";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);

//         $input = "trlcp(\"anycontext\", \"undefined word\", \"undefined words\", 2)";
//         $expected = "trlp(\"undefined word\", \"undefined words\", 2)";
//         $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
//         $this->assertEquals($expected, $result);
    }

    public function testRealTranslation()
    {
        $this->markTestIncomplete();
        $input = "trlKwf('Save')";
        $expected = "trl('Speichern')";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'de');
        $this->assertEquals($expected, $result);

        $input = "trlpKwf('reply', 'replies', 2)";
        $expected = "trlp('Antwort', 'Antworten', 2)";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'de');
        $this->assertEquals($expected, $result);

        $input = "trlKwf('Save')";
        $expected = "trl('Save')";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);

        $input = "trlcKwf('time', 'On')";
        $expected = "trl('Am')";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'de');
        $this->assertEquals($expected, $result);

        $input = "trlcKwf('forum', 'Location')";
        $expected = "trl('Location')";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'en');
        $this->assertEquals($expected, $result);

        $input = str_repeat(' ', 10015)." trlKwf('Info')";
        $result = $this->_jsLoader->trlLoad($input, Kwf_Trl_Parser_JsParser::parseContent($input), 'de');
        $this->assertNotContains('trlKwf', $result);
    }

}
