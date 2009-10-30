<?php
/**
 * @group Helper
 * @group Helper_HighlightTerms
 */
class Vps_View_HighlightTermsTest extends PHPUnit_Framework_TestCase
{
    private $_text = '';

    public function setUp()
    {
        $this->_text = "Für. Dies ist ein Text für das Highlighten des Textes. \n"
            ."Es können auch Umlaute vorkommen, die gut für das Testen von utf8 sind.\n"
            ."Auch Zeilenumbrüche sind drin um zu sehen, ob trotzem alles funktioniert.\n"
            ."Ein längerer Aufsatz hierfür sollte es eigentlich nicht werden, allerdings "
            ."benötigen wir für das korrekte Testen schon etwas mehr als 200 Zeichen. "
            ."Aber ich denke mittlerweile haben wir genug für einen korrekten Test.\n"
            ." Fürwahr, das könnte gut Für mich sein. für";
    }

    public function testHighlighting()
    {
        $searchWord = 'für';
        $h = new Vps_View_Helper_HighlightTerms();
        $res = $h->highlightTerms($searchWord, $this->_text, array('maxReturnLength' => 0));

        $highlights = mb_substr_count($res, '<span ');
        $this->assertEquals(7, $highlights);

        $this->assertRegExp('/<span class="highlightTerms highlightTerm1">für<\/span>/', $res);
        $this->assertRegExp('/<span class="highlightTerms highlightTerm1">Für<\/span>/', $res);
        $this->assertNotRegExp('/hier<span class="highlightTerms highlightTerm1">für<\/span>/', $res);
        $this->assertNotRegExp('/<span class="highlightTerms highlightTerm1">Für<\/span>wahr/', $res);
    }

    public function testShorting()
    {
        $searchWord = 'für';
        $h = new Vps_View_Helper_HighlightTerms();
        $res = $h->highlightTerms($searchWord, $this->_text, array('maxReturnLength' => 200, 'maxReturnBlocks' => 4));

        $highlights = mb_substr_count($res, '<span ');
        $this->assertEquals(4, $highlights);

        $highlights = mb_substr_count($res, ' ... ');
        $this->assertEquals(3, $highlights);

        $this->assertLessThanOrEqual(200, mb_strlen(strip_tags($res)));
    }
}
