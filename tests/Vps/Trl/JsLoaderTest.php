<?php
/**
 * @group jstrl
 * @group trl
 *
 */
class Vps_Trl_JsLoaderTest extends Vps_Test_TestCase
{
    private $_jsLoader;
    public function setUp()
    {
        parent::setUp();
        $this->_jsLoader = new Vps_Trl_JsLoader();
    }

    public function testTrl()
    {
        $input = "trl('undefined word')";
        $expected = "trl('undefined word')";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);

        $input = "trl(\"undefined word\")";
        $expected = "trl(\"undefined word\")";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);
    }

    public function testTrlc()
    {
        $input = "trlc('anycontext', 'undefined word')";
        $expected = "trl('undefined word')";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);

        $input = "trlc(\"anycontext\", \"undefined word\")";
        $expected = "trl(\"undefined word\")";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);
    }

    public function testTrlp()
    {
        $input = "trlp('undefined word', 'undefined words', 2)";
        $expected = "trlp('undefined word', 'undefined words', 2)";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);

        $input = "trlp(\"undefined word\", \"undefined words\", 2)";
        $expected = "trlp(\"undefined word\", \"undefined words\", 2)";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);
    }

    public function testTrlcp()
    {
        $input = "trlcp('anycontext', 'undefined word', 'undefined words', 2)";
        $expected = "trlp( 'undefined word', 'undefined words', 2)";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);

        $input = "trlcp(\"anycontext\", \"undefined word\", \"undefined words\", 2)";
        $expected = "trlp( \"undefined word\", \"undefined words\", 2)";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);
    }

    public function testRealTranslation()
    {
        $input = "trlVps('Save')";
        $expected = "trl('Speichern')";
        $result = $this->_jsLoader->trlLoad($input, 'de');
        $this->assertEquals($expected, $result);

        $input = "trlpVps('reply', 'replies', 2)";
        $expected = "trlp('Antwort', 'Antworten', 2)";
        $result = $this->_jsLoader->trlLoad($input, 'de');
        $this->assertEquals($expected, $result);

        $input = "trlVps('Save')";
        $expected = "trl('Save')";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);

        $input = "trlcVps('time', 'On')";
        $expected = "trl('Am')";
        $result = $this->_jsLoader->trlLoad($input, 'de');
        $this->assertEquals($expected, $result);

        $input = "trlcVps('forum', 'Location')";
        $expected = "trl('Location')";
        $result = $this->_jsLoader->trlLoad($input, 'en');
        $this->assertEquals($expected, $result);

        $input = str_repeat(' ', 10015)." trlVps('Info')";
        $result = $this->_jsLoader->trlLoad($input, 'de');
        $this->assertNotContains('trlVps', $result);
    }

}