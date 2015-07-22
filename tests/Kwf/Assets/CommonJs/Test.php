<?php
class Kwf_Assets_CommonJs_Test extends Kwf_Test_TestCase
{
    private $_list;
    private $_package;
    public function setUp()
    {
        parent::setUp();
        $this->_list = new Kwf_Assets_CommonJs_TestProviderList();
        $this->_package = new Kwf_Assets_Package($this->_list, 'A');
    }

    public function testPackageContents()
    {
        /*
        A.js (commonjsentry)
         - (commonjs) B.js
                     - (requires) C.css
                     - (requires) D.js
        */
        $c = $this->_package->getPackageContents('text/javascript', 'en', 0, false)->getFileContents();

        //contains non-commonjs PLUS commonjs, commonjs starts with window.require
        $pos = strpos($c, 'window.require');
        $nonCommonjs = substr($c, 0, $pos);
        $commonjs = substr($c, $pos);
        $this->assertContains('console.log(A)', $commonjs);
        $this->assertContains('console.log(B)', $commonjs);
        $this->assertContains('console.log(D)', $nonCommonjs);

        $this->assertNotContains('console.log(A)', $nonCommonjs);
        $this->assertNotContains('console.log(B)', $nonCommonjs);
        $this->assertNotContains('console.log(D)', $commonjs);

        $c = $this->_package->getPackageContents('text/css', 'en', 0, false)->getFileContents();
        $c = str_replace("\n", '', $c);
        $this->assertEquals($c, 'C');
    }
}
