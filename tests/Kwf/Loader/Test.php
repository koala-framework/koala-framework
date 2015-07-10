<?php
/**
 * @group kwfLoader
 */
class Kwf_Loader_Test extends Kwf_Test_TestCase
{
    public function testClassExistsExisting()
    {
        $this->assertTrue(class_exists('Kwf_Loader_TestClass'));
    }

    public function testClassExistsNotExisting()
    {
        $this->assertFalse(class_exists('Kwf_Loader_TestClassNotExisting'));
    }

    public function testInstanciateClass()
    {
        new Kwf_Loader_TestClass2();
    }

    /* macht einen fatal error, ist somit untestbar
     * ...hab ich schon erwäht dass php scheiße ist?
    public function testInstanciateClassNotExisting()
    {
        new Kwf_Loader_TestClassNotExisting();
    }
    */

    public function findFileProvider()
    {
        return array(
            array(array('A_' => array('vendor/a/a')), array(), 'A_X', 'vendor/a/a/A/X.php'),
            array(array('A_' => array('vendor/a/a')), array(), 'A_X_Y', 'vendor/a/a/A/X/Y.php'),
            array(array('A_' => array('vendor/a/a')), array(), 'A_X_Y_Z', 'vendor/a/a/A/X/Y/Z.php'),
            array(array('A_B_' => array('vendor/a/b')), array(), 'A_B_X', 'vendor/a/b/A/B/X.php'),
            array(array('A_B_' => array('vendor/a/b')), array(), 'A_B_X_Y', 'vendor/a/b/A/B/X/Y.php'),
            array(array('A_B_' => array('vendor/a/b')), array(), 'A_B_X_Y_Z', 'vendor/a/b/A/B/X/Y/Z.php'),

            array(array('A\\' => array('vendor/a/a')), array(), 'A\\X', 'vendor/a/a/A/X.php'),
            array(array('A\\' => array('vendor/a/a')), array(), 'A\\X\\Y', 'vendor/a/a/A/X/Y.php'),
            array(array('A\\' => array('vendor/a/a')), array(), 'A\\X\\Y\\Z', 'vendor/a/a/A/X/Y/Z.php'),
            array(array('A\\B\\' => array('vendor/a/b')), array(), 'A\\B\\X', 'vendor/a/b/A/B/X.php'),
            array(array('A\\B\\' => array('vendor/a/b')), array(), 'A\\B\\X\\Y', 'vendor/a/b/A/B/X/Y.php'),
            array(array('A\\B\\' => array('vendor/a/b')), array(), 'A\\B\\X\\Y\\Z', 'vendor/a/b/A/B/X/Y/Z.php'),
            array(array('A\\B\\C\\' => array('vendor/a/b-c')), array(), 'A\\B\\C\\X', 'vendor/a/b-c/A/B/C/X.php'),
            array(array('A\\B\\C\\' => array('vendor/a/b-c')), array(), 'A\\B\\C\\X\\Y', 'vendor/a/b-c/A/B/C/X/Y.php'),

            //psr-4:
            array(array(), array('A\\' => array('vendor/a/a')), 'A\\X', 'vendor/a/a/X.php'),
            array(array(), array('A\\' => array('vendor/a/a')), 'A\\X\\Y', 'vendor/a/a/X/Y.php'),
            array(array(), array('A\\' => array('vendor/a/a')), 'A\\X\\Y\\Z', 'vendor/a/a/X/Y/Z.php'),
            array(array(), array('A\\B\\' => array('vendor/a/b')), 'A\\B\\X', 'vendor/a/b/X.php'),
            array(array(), array('A\\B\\' => array('vendor/a/b')), 'A\\B\\X\\Y', 'vendor/a/b/X/Y.php'),
            array(array(), array('A\\B\\' => array('vendor/a/b')), 'A\\B\\X\\Y\\Z', 'vendor/a/b/X/Y/Z.php'),
            array(array(), array('A\\B\\C\\' => array('vendor/a/b-c')), 'A\\B\\C\\X', 'vendor/a/b-c/X.php'),
            array(array(), array('A\\B\\C\\' => array('vendor/a/b-c')), 'A\\B\\C\\X\\Y', 'vendor/a/b-c/X/Y.php'),
            array(array(), array('A\\B\\C\\' => array('vendor/a/b-c')), 'A\\B\\C\\X\\Y\\Z', 'vendor/a/b-c/X/Y/Z.php'),

            array(array('PHPExcel' => array('vendor/phpoffice/phpexel/Classes')), array(), 'PHPExcel', 'vendor/phpoffice/phpexel/Classes/PHPExcel.php'),
            array(array('Sepia' => array('vendor/sepia/po-parser/src')), array(), 'Sepia\\PoParser', 'vendor/sepia/po-parser/src/Sepia/PoParser.php'),
        );
    }

    /**
     * @dataProvider findFileProvider
     */
    public function testFindFile(array $composerNamespaces, array $psr4Namespaces, $class, $expectedFile)
    {
        foreach ($composerNamespaces as &$dirs) {
            foreach ($dirs as &$dir) {
                $dir = getcwd().'/'.$dir;
            }
        }
        unset($dirs);
        unset($dir);

        foreach ($psr4Namespaces as &$dirs) {
            foreach ($dirs as &$dir) {
                $dir = getcwd().'/'.$dir;
            }
        }
        unset($dirs);
        unset($dir);

        $namespaces = Kwf_Loader::_prepareNamespaces($composerNamespaces, $psr4Namespaces);
        $file = Kwf_Loader::_findFile($class, $namespaces, array());
        $this->assertEquals(getcwd().'/'.$expectedFile, $file);
    }
}
