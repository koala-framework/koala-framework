<?php
/**
 * Wird in der Kwf_Test_TestSuite automatisch hinzugefügt, damit er auch im web ausgeführt wird
 *
 * @group javascript
 * @group slow
 */
class Kwf_Js_SyntaxTest extends Kwf_Test_TestCase
{
    // in den leeren arrays müssen die pfade ausgehend vom kwf / web hauptverzeichnis sein
    // ohne führendem slash
    protected $_whitelist = array(
        'console.log' => array(
            'Kwf_js/Debug/Activator.js'
        ),
        'debugger' => array(),
        ',}' => array()
    );

    // absoluter pfad zum scannen, ohne abschließenden slash
    // wenn im kwf getestet wird, gibts den kwf pfad zurück
    // wenn im web getestet wird, den web pfad
    private function _getScanPath() {
        return KWF_PATH;
    }

    public function testJsFailures()
    {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_getScanPath()));
        foreach ($it as $file) {
            if (preg_match('#/(cache|tests|vendor|build|node_modules)/#', $file->getPathname())) continue;
            if (preg_match('#/Form/BasicForm\.js$#', $file->getPathname())) continue;
            if (!preg_match('/\.js$/i', $file->getFilename())) continue;

            if (($errors = $this->_fileHasErrors($file->getPathname())) !== false) {
                $this->fail('Errors in Javascript-File: '.$file->getPathname()."\n".implode("\n", $errors));
            }
        }
    }

    private function _fileHasErrors($path)
    {
        $errors = array();

        $contents = file_get_contents($path);
        // mehrzeilige kommentare entfernen
        $contents = preg_replace('/\/\*.*?\*\//ims', '', $contents);
        // einfache kommentare entfernen
        $contents = preg_replace('/\/\/.+?$/im', '', $contents);
        // einen ein-zeilen-string machen, ohne whitespaces
        $contents = preg_replace('/\s+/ims', '', $contents);

        if (!$this->_isWhitelist('debugger', $path)
            && strpos($contents, 'debugger') !== false
        ) {
            $errors[] = "'debugger' exists";
        }
        if (!$this->_isWhitelist('console.log', $path)
            && strpos($contents, 'console.log') !== false
        ) {
            $errors[] = "'console.log' exists";
        }
        if (!$this->_isWhitelist(',}', $path)
            && strpos($contents, ',}') !== false
        ) {
            $errors[] = "', }' exists";
        }

        if (empty($errors)) return false;
        return $errors;
    }

    private function _isWhitelist($list, $path) {
        if (in_array(str_replace($this->_getScanPath().'/', '', $path), $this->_whitelist[$list])
            || in_array(str_replace($this->_getScanPath().'/kwf-lib/', '', $path), $this->_whitelist[$list])
            || strpos($path, 'jquery') !== false
        ) {
            return true;
        }
        return false;
    }
}
