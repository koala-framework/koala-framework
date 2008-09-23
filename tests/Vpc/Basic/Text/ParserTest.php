<?php
class Vpc_Basic_Text_ParserTest extends PHPUnit_Framework_TestCase
{
    private $_parser;
    public function setUp()
    {
        $this->_parser = new Vpc_Basic_Text_Parser(null);
        $this->_parser->setEnableTagsWhitelist(true);
        $this->_parser->setEnableStyles(true);
    }
    public function testParser()
    {
        $out = $this->_parser->parse('<br />');
        $this->assertEquals('<br />', $out);

        $out = $this->_parser->parse('<p>foo1</p>');
        $this->assertEquals('<p>foo1</p>', $out);

        $out = $this->_parser->parse('<p class="MsoNormal">foo2</p>');
        $this->assertEquals('<p>foo2</p>', $out);

        $out = $this->_parser->parse('<p style="xxxyy"><strong>foo1</strong></p>');
        $this->assertEquals('<p><strong>foo1</strong></p>', $out);

        $out = $this->_parser->parse('<p foo="bar">foo3</p>');
        $this->assertEquals('<p>foo3</p>', $out);

        $out = $this->_parser->parse('<foo foo="bar">foo4</foo>');
        $this->assertEquals('foo4', $out);

        $out = $this->_parser->parse('<h1 foo="bar">foo5</h1>');
        $this->assertEquals('<h1>foo5</h1>', $out);

        $out = $this->_parser->parse('<span foo="bar">foo6</span>');
        $this->assertEquals('foo6', $out);

        $out = $this->_parser->parse('<h1>foo7</h1>');
        $this->assertEquals('<h1>foo7</h1>', $out);

        $out = $this->_parser->parse('<h1 class="foo">foo7</h1>');
        $this->assertEquals('<h1>foo7</h1>', $out);

        $out = $this->_parser->parse('<h1 class="style1">foo7</h1>');
        $this->assertEquals('<h1 class="style1">foo7</h1>', $out);

        $out = $this->_parser->parse('<h1 class="style1" asdf="foo">foo7</h1>');
        $this->assertEquals('<h1 class="style1">foo7</h1>', $out);

        $out = $this->_parser->parse('<strong class="style1">foo7</strong>');
        $this->assertEquals('<strong>foo7</strong>', $out);

        $out = $this->_parser->parse('<h1 class="style1" asdf="foo">f<span>oo</span>7</h1>');
        $this->assertEquals('<h1 class="style1">foo7</h1>', $out);

        $out = $this->_parser->parse('<h1 class="style1" asdf="foo">f<span class="style1">oo</span>7</h1>');
        $this->assertEquals('<h1 class="style1">f<span class="style1">oo</span>7</h1>', $out);

        $this->_parser->setEnableStyles(false);
        $out = $this->_parser->parse('<h1>foo8</h1>');
        $this->assertEquals('foo8', $out);

        $out = $this->_parser->parse('<span class="style1">foo8</span>');
        $this->assertEquals('foo8', $out);

        $out = $this->_parser->parse('<p style="xxxyy"><strong>foo1</strong></p>');
        $this->assertEquals('<p><strong>foo1</strong></p>', $out);

        $out = $this->_parser->parse('<script language="text/javascript">foo7</script>');
        $this->assertEquals('', $out);

        $out = $this->_parser->parse('foo<script language="text/javascript">foo7</script>bar');
        $this->assertEquals('foobar', $out);

        $out = $this->_parser->parse('foo<script language="text/javascript">f<p>oo</p>7</script>bar');
        $this->assertEquals('foobar', $out);

        $out = $this->_parser->parse('foo<script>bb<script>hello world</script>aa</script>bar');
        $this->assertEquals('foobar', $out);

        $out = $this->_parser->parse('<!-- [if !supportLists] -->');
        $this->assertEquals('', $out);

    }
}
