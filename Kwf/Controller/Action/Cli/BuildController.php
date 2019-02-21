<?php
class Kwf_Controller_Action_Cli_BuildController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "build";
    }

    public static function getHelpOptions()
    {
        $types = array();
        foreach (Kwf_Util_Build::getInstance()->getTypes() as $t) {
            $types[] = $t->getTypeName();
        }
        return array(
            array(
                'param'=> 'type',
                'value'=> implode(',', $types),
                'valueOptional' => true,
                'help' => 'what to build'
            )
        );
    }

    private function _checkNodeBins()
    {
        echo "\n";
        //check if node-sass is working correctly, if not try to npm rebuild
        //works around possible issues with binaries used by node-sass
        exec("./vendor/bin/node node_modules/.bin/node-sass --version 2>&1", $output, $retVal);
        if ($retVal) {
            //node-sass doesn't work
            passthru("./vendor/bin/npm rebuild node-sass", $retVal);
            if ($retVal) {
                throw new Kwf_Exception("node-sass rebuild failed");
            }
        }
    }

    public function indexAction()
    {
        if (file_exists(VENDOR_PATH.'/koala-framework/koala-framework/node_modules')) {
            throw new Kwf_Exception('Please delete node_modules folder from koala-framework/koala-framework. All node packages has moved into ./node_modules');
        }

        $this->_checkNodeBins();

        $options = array(
            'types' => $this->_getParam('type'),
            'output' => true,
            'refresh' => true,
            'excludeTypes' => ''
        );
        if (Kwf_Config::getValue('debug.webpackDevServer')) {
            $options['excludeTypes'] .= ',assets';
        }
        if (is_string($this->_getParam('exclude-type'))) {
            $options['excludeTypes'] .= ','.$this->_getParam('exclude-type');
        }
        $options['excludeTypes'] = trim($options['excludeTypes'], ',');
        if (!Kwf_Util_Build::getInstance()->build($options)) {
            exit(1);
        } else {
            exit;
        }
    }

    public function assetsAction()
    {
        $this->_checkNodeBins();

        $options = array(
            'types' => 'assets',
            'output' => true,
            'refresh' => true,
        );
        if (!Kwf_Util_Build::getInstance()->build($options)) {
            exit(1);
        } else {
            exit;
        }
    }

    public function clearAction()
    {
        $paths = array(
            'cache/uglifyjs',
            'cache/commonjs',
            'cache/assetdeps',
            sys_get_temp_dir().'/kwf-uglifyjs/',
            'build'
        );
        foreach ($paths as $path) {
            echo "clearing $path...\n";
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                if ($fileinfo->getFilename() != '.gitignore' && $fileinfo->getFilename() != '.git') {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    $todo((string)$fileinfo);
                }
            }
        }
        exit;
    }
}
