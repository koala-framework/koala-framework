<?php
/**
 * @group trl
 */
class Vps_Trl_TrlTest extends Vps_Test_TestCase
{
    private $_trlObject;
    public function setUp()
    {
        parent::setUp();
        $this->_trlObject = new Vps_Trl();
    }

    public function testTrlParsePhpStatic()
    {
        $values = $this->_trlObject->parse("trlStatic('testWord')");
        $this->assertEquals('web', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);

        $values = $this->_trlObject->parse("trlcStatic(\"testContext\", \"testWord\")");
        $this->assertEquals('web', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testContext', $values[0]['context']);

        $values = $this->_trlObject->parse("trlpStatic('testWord', 'testWords', 3)");
        $this->assertEquals('web', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testWords', $values[0]['plural']);

        $values = $this->_trlObject->parse("trlcpStatic('testContext', 'testWord', 'testWords', 3)");
        $this->assertEquals('web', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testWords', $values[0]['plural']);
        $this->assertEquals('testContext', $values[0]['context']);
        $this->assertEquals('trlcp', $values[0]['type']);
        $this->assertEquals("trlcpStatic('testContext', 'testWord', 'testWords', 3)", $values[0]['before']);

        $values = $this->_trlObject->parse("trlVpsStatic('testWord')");
        $this->assertEquals('vps', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);

        $values = $this->_trlObject->parse("trlcVpsStatic(\"testContext\", \"testWord\")");
        $this->assertEquals('vps', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testContext', $values[0]['context']);

        $values = $this->_trlObject->parse("trlpVpsStatic('testWord', 'testWords', 3)");
        $this->assertEquals('vps', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testWords', $values[0]['plural']);

        $values = $this->_trlObject->parse("trlcpVpsStatic('testContext', 'testWord', 'testWords', 3)");
        $this->assertEquals('vps', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testWords', $values[0]['plural']);
        $this->assertEquals('testContext', $values[0]['context']);
        $this->assertEquals('trlcp', $values[0]['type']);
        $this->assertEquals("trlcpVpsStatic('testContext', 'testWord', 'testWords', 3)", $values[0]['before']);

    }

    public function testTrlParsePhp()
    {
        $values = $this->_trlObject->parse('trl("\n")');
        $this->assertEquals(Vps_Trl::ERROR_INVALID_STRING, $values[0]['error_short']);

        $values = $this->_trlObject->parse('trl("")');
        $this->assertEquals(Vps_Trl::ERROR_WRONG_NR_OF_ARGUMENTS, $values[0]['error_short']);

        $values = $this->_trlObject->parse('trlc("aaa")');
        $this->assertEquals(Vps_Trl::ERROR_WRONG_NR_OF_ARGUMENTS, $values[0]['error_short']);

        $values = $this->_trlObject->parse('trlc("aaa", array("hallo")');
        $this->assertEquals(Vps_Trl::ERROR_WRONG_NR_OF_ARGUMENTS, $values[0]['error_short']);

        $values = $this->_trlObject->parse('trl("hallo");
        trlc("context", "text");
        trlp("one beer", "{0} beers") asdfjklkasjf asklfjdksadljf trl("asdf"."asdf") asklfjsdalkfj trl("test"))');
        $this->assertEquals("hallo", $values[0]['text']);
        $this->assertEquals("context", $values[1]['context']);
        $this->assertEquals("text", $values[1]['text']);
        $this->assertEquals("one beer", $values[2]['text']);
        $this->assertEquals("{0} beers", $values[2]['plural']);
        $this->assertEquals(Vps_Trl::ERROR_INVALID_CHAR, $values[3]['error_short']);
        $this->assertEquals("test", $values[4]['text']);

        $values = $this->_trlObject->parse('trl("test"."foo")');
        $this->assertEquals(true, $values[0]['error']);
        $this->assertEquals(Vps_Trl::ERROR_INVALID_CHAR, $values[0]['error_short']);

        $values = $this->_trlObject->parse('trl("test$foo")');
        $this->assertEquals(Vps_Trl::ERROR_INVALID_STRING, $values[0]['error_short']);

        $values = $this->_trlObject->parse('trl("test\$foo")');
        $this->assertEquals('test\$foo', $values[0]['text']);

        $values = $this->_trlObject->parse('asdfdsa
        fasdfasdf
        asdf a
        s
        trl(\'check\')
        asfsdafa

        asdfsafd
        trl(\'test$foo\')');
        $this->assertEquals('check', $values[0]['text']);
        $this->assertEquals(5, $values[0]['linenr']);
        $this->assertEquals('test$foo', $values[1]['text']);
        $this->assertEquals(9, $values[1]['linenr']);

        //gleicher test mit fehler
        $values = $this->_trlObject->parse('asdfdsa
        fasdfasdf
        asdf a
        s
        trl(\'check\')
        asfsdafa

        asdfsafd
        trl("test"."foo")');
        $this->assertEquals('check', $values[0]['text']);
        $this->assertEquals(5, $values[0]['linenr']);
        $this->assertEquals(Vps_Trl::ERROR_INVALID_CHAR, $values[1]['error_short']);
        $this->assertEquals(9, $values[1]['linenr']);
        $values = $this->_trlObject->parse("trl(\"test\nfoo\")");
        $this->assertEquals(Vps_Trl::ERROR_INVALID_STRING, $values[0]['error_short']);

        $values = $this->_trlObject->parse("trl('testWord')");
        $this->assertEquals('web', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);

        $values = $this->_trlObject->parse("trlVps('testWord')");
        $this->assertEquals('vps', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);

        $values = $this->_trlObject->parse("trlc(\"testContext\", \"testWord\")");
        $this->assertEquals('web', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testContext', $values[0]['context']);

        $values = $this->_trlObject->parse("trlcVps(\"testContext\", \"testWord\")");
        $this->assertEquals('vps', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testContext', $values[0]['context']);

        $values = $this->_trlObject->parse("trlp('testWord', 'testWords', 3)");
        $this->assertEquals('web', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testWords', $values[0]['plural']);
        $values = $this->_trlObject->parse("trlpVps('testWord', 'testWords', 3)");
        $this->assertEquals('vps', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testWords', $values[0]['plural']);

        $values = $this->_trlObject->parse("trlcpVps('testContext', 'testWord', 'testWords', 3)");
        $this->assertEquals('vps', $values[0]['source']);
        $this->assertEquals('testWord', $values[0]['text']);
        $this->assertEquals('testWords', $values[0]['plural']);
        $this->assertEquals('testContext', $values[0]['context']);
        $this->assertEquals('trlcp', $values[0]['type']);
        $this->assertEquals("trlcpVps('testContext', 'testWord', 'testWords', 3)", $values[0]['before']);

        //more complicated tests
        $values = $this->_trlObject->parse("trl('testWord {0} and {1}', array('word1' and 'word2')");
        $this->assertEquals('web', $values[0]['source']);
        $this->assertEquals('testWord {0} and {1}', $values[0]['text']);

        $values = $this->_trlObject->parse('trl("te\'st")');
        $this->assertEquals("te'st", $values[0]['text']);

        $values = $this->_trlObject->parse('trl("testW\"ord {0} and {1}")');
        $this->assertEquals('testW"ord {0} and {1}', $values[0]['text']);

        $values = $this->_trlObject->parse("trl('te\"st')");
        $this->assertEquals('te"st', $values[0]['text']);

        $values = $this->_trlObject->parse("trl('test')");
        $this->assertEquals('test', $values[0]['text']);

        $values = $this->_trlObject->parse('trl(\'test\')');
        $this->assertEquals('test', $values[0]['text']);

        $values = $this->_trlObject->parse('trl("testWord {0} and {1}", array("word1", "word2");');
        $this->assertEquals("testWord {0} and {1}", $values[0]['text']);

        $values = $this->_trlObject->parse('trlc("context", "text"); trl("text2");');
        $this->assertEquals("context", $values[0]['context']);
    }

    public function testTrlParseJs ()
    {
        $values = $this->_trlObject->parse('trlc("context", "{0} days a week", [5])', 'js');
        $this->assertEquals('context', $values[0]['context']);
        $this->assertEquals('{0} days a week', $values[0]['text']);

        $values = $this->_trlObject->parse('trl("hallo")', 'js');
        $this->assertEquals('hallo', $values[0]['text']);

        $values = $this->_trlObject->parse('trlp("one error found", "{0} errors found", [2])', 'js');
        $this->assertEquals('one error found', $values[0]['text']);
        $this->assertEquals('{0} errors found', $values[0]['plural']);

        $values = $this->_trlObject->parse('trlcp("errors", "one error found", "{0} errors found", [2])', 'js');
        $this->assertEquals('errors', $values[0]['context']);
        $this->assertEquals('one error found', $values[0]['text']);
        $this->assertEquals('{0} errors found', $values[0]['plural']);

        $values = $this->_trlObject->parse('trl("signs{}[])=!")', 'js');
        $this->assertEquals('signs{}[])=!', $values[0]['text']);

        $values = $this->_trlObject->parse('trl("signs{}[])=!$")', 'js');
        $this->assertEquals("signs{}[])=!$", $values[0]['text']);

        $values = $this->_trlObject->parse('trl("signs{}[])\n=!")', 'js');
        $this->assertEquals(true, $values[0]['error']);

        $values = $this->_trlObject->parse('trl("signs{}[])
        =!")', 'js');
        $this->assertEquals(true, $values[0]['error']);
    }

    public function testTrlParseJsLargeString()
    {
        $input = str_repeat(' ', 10015)." trlVps('Info')";
        $result = $this->_trlObject->parse($input, 'js');
        $this->assertEquals(1, count($result));

        $input = str_repeat(' ', 11000)." trlVps('Info')";
        $result = $this->_trlObject->parse($input, 'js');
        $this->assertEquals(1, count($result));

        $input = str_repeat(' ', 7998)."trlVps('Info')".
                 str_repeat(' ', 11000)."trlVps('Foo')";
        $result = $this->_trlObject->parse($input, 'js');
        $this->assertEquals(2, count($result));

        $input = str_repeat(' ', 9997)."trlVps('Info')".
                 str_repeat(' ', 11000)."trlVps('Foo')";
        $result = $this->_trlObject->parse($input, 'js');
        $this->assertEquals(2, count($result));

        $input = str_repeat(' ', 9797)."trlVps('Info')".
                 str_repeat(' ', 11000)."trlVps('Foo')";
        $result = $this->_trlObject->parse($input, 'js');
        $this->assertEquals(2, count($result));

        $input = str_repeat(' ', 9797)."trlVps('Info')".
                 str_repeat(' ', 11000)."trlVps('Foo')".
                 str_repeat(' ', 11000)."trlVps('foobar')".
                 str_repeat(' ', 6879)."trlVps('lala')";
        $result = $this->_trlObject->parse($input, 'js');
        $this->assertEquals(4, count($result));
    }

    public function testTrlInsertToXml()
    {

        $tmpfname = tempnam("/tmp", "XMLTEST");
        file_put_contents($tmpfname, "<trl></trl>");


        $modelWeb = new Vps_Model_FnF();
        $modelWeb->setData(array(
            array('id' => 1, 'en' => 'foo', 'de' => 'dings'),
            array('id' => 2, 'en' => 'foobar', 'de' => 'dingsbums')
        ));

        $modelVps = new Vps_Model_FnF(array(
            'columns' => array('id', 'en', 'de'),
            'data' => array(
                array('id' => 1, 'en' => 'foo', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'de' => 'dingsbums')
            )
        ));
        $parser = new Vps_Trl_Parser($modelVps, $modelWeb, 'vps');
        $parser->setLanguages(array('en', 'de'));
        $parser->insertToXml(array(array('text' => 'newFoo', 'source' => 'vps')), $tmpfname, 'vps');
        $select = $modelVps->select();
        $select->whereEquals('en', 'newFoo');
        $row = $modelVps->getRows($select)->current();
        $this->assertEquals(3, $row->id);
        $this->assertEquals('', $row->de);

        //insert same again
        $this->assertEquals(3, $modelVps->getRows()->count());
        $parser->insertToXml(array(array('text' => 'newFoo', 'source' => 'web')), $tmpfname, 'vps');
        $this->assertEquals(3, $modelVps->getRows()->count());

        //insert other
        $this->assertEquals(3, $modelVps->getRows()->count());
        $parser->insertToXml(array(array('text' => 'newFoo2', 'source' => 'vps')), $tmpfname, 'vps');
        $this->assertEquals(4, $modelVps->getRows()->count());

         //insert other
        $this->assertEquals(4, $modelVps->getRows()->count());
        $parser->insertToXml(array(array('text' => 'This field should only contain letters and _', 'source' => 'vps')), $tmpfname, 'vps');
        $this->assertEquals(5, $modelVps->getRows()->count());

         //insert other
        $this->assertEquals(5, $modelVps->getRows()->count());
        $parser->insertToXml(array(array('text' => 'This field should only contain letters and _', 'source' => 'vps')), $tmpfname, 'vps');
        $this->assertEquals(5, $modelVps->getRows()->count());

        unlink($tmpfname);


    }

    public function testTrlTranslation ()
    {
        $modelVps = new Vps_Model_FnF(array(
            'columns' => array(),
            'data' => array(
                array('id' => 1, 'en' => 'foo', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'de' => 'dingsbums'),
                array('id' => 3, 'en' => 'foobar', 'en_plural' => 'foobars', 'de' => 'dingsbums', 'de_plural' => 'dingsbumse'),
                array('id' => 4, 'context' => 'special', 'en' => 'special foobar', 'en_plural' => 'special foobars', 'de' => 'spezial dingsbums', 'de_plural' => 'spezial dingsbumse'),
                array('id' => 5, 'context' => 'special', 'en' => 'special foobar', 'de' => 'spezial dingsbums'),
                array('id' => 6, 'en' => '{0} foo', 'de' => '{0} dings')
            )
        ));
        $modelWeb = new Vps_Model_FnF(array(
            'data' => array(
            )
        ));
        $config['modelVps'] = $modelVps;
        $config['modelWeb'] = $modelWeb;
        $this->_trlObject = new Vps_Trl($config);
        $this->_trlObject->setLanguages(array('de', 'en'));
        $this->_trlObject->setModel($modelVps, 'vps');
        $this->assertEquals('dingsbums', $this->_trlObject->trl('foobar', array(), 'vps', 'de'));
        $this->assertEquals('dingsbumse', $this->_trlObject->trlp('foobar', 'foobars', array(2), 'vps', 'de'));
        $this->assertEquals('spezial dingsbums', $this->_trlObject->trlcp('special', 'special foobar', 'special foobars', array(1), 'vps', 'de'));
        $this->assertEquals('spezial dingsbums', $this->_trlObject->trlc('special', 'special foobar', array(), 'vps', 'de'));
        $this->assertEquals('5 dings', $this->_trlObject->trl('{0} foo', array(5), 'vps', 'de'));
    }

    public function testTrlTranslationNotFound()
    {
        $modelVps = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 1, 'en' => 'foo', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'de' => 'dingsbums'),
                array('id' => 3, 'en' => 'foobar', 'en_plural' => 'foobars', 'de' => 'dingsbums', 'de_plural' => 'dingsbumse'),
                array('id' => 4, 'context' => 'special', 'en' => 'special foobar', 'en_plural' => 'special foobars', 'de' => 'spezial dingsbums', 'de_plural' => 'spezial dingsbumse'),
                array('id' => 5, 'context' => 'special', 'en' => 'special foobar', 'de' => 'spezial dingsbums'),
                array('id' => 6, 'en' => '{0} foo', 'de' => '{0} dings')
            )
        ));

        $modelWeb = new Vps_Model_FnF(array(
            'data' => array(
            )
        ));
        $config['modelVps'] = $modelVps;
        $config['modelWeb'] = $modelWeb;
        $this->_trlObject = new Vps_Trl($config);
        $this->_trlObject->setLanguages(array('de', 'en'));
        $this->_trlObject->setModel($modelVps, 'vps');
        $this->assertEquals('notfound', $this->_trlObject->trl('notfound', array(), 'vps'));
    }

    public function testTrlParserCleanup()
    {
        $modelWeb = new Vps_Trl_TestModel(array(
            'columns' => array('id', 'en', 'de'),
            'data' => array(
                array('id' => 1, 'en' => 'foo', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'de' => 'dingsbums')
            )
        ));

        $modelVps = new Vps_Model_FnF(array(
            'columns' => array('id', 'en', 'de'),
            'data' => array(
                array('id' => 1, 'en' => 'foo', 'de' => 'dings'),
                array('id' => 2, 'en' => 'foobar', 'de' => 'dingsbums')
            )
        ));

        $parser = new Vps_Trl_Parser($modelVps, $modelWeb, 'vps', 'vps');
        $parser->setLanguages(array('en', 'de'));
        $path = VPS_PATH."/tests/Vps/Trl/TestParseFolder";
        $results = $parser->parse(array('web' => $path, 'vps' => $path), true);

        $this->assertEquals(1, count($results['added']['Vps_Model_FnF']));
        $this->assertEquals(1, count($results['deleted']['Vps_Model_FnF']));
        $this->assertEquals("foo", $results['deleted']['Vps_Model_FnF'][0]);

    }

}
