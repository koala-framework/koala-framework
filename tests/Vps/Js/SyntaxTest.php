<?php
/**
 * Wird in der Vps_Test_TestSuite automatisch hinzugefügt, damit er auch im web ausgeführt wird
 *
 * @group javascript
 * @group slow
 */
class Vps_Js_SyntaxTest extends Vps_Test_TestCase
{
    // in den leeren arrays müssen die pfade ausgehend vom vps / web hauptverzeichnis sein
    // ohne führendem slash
    protected $_whitelist = array(
        'console.log' => array(
            'Vps_js/Debug/Activator.js'
        ),
        'debugger' => array(),
        ',}' => array()
    );

    // absoluter pfad zum scannen, ohne abschließenden slash
    // wenn im vps getestet wird, gibts den vps pfad zurück
    // wenn im web getestet wird, den web pfad
    private function _getScanPath() {
        return getcwd();
    }

    public function testJsFailures()
    {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_getScanPath()));
        foreach ($it as $file) {
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
        if (in_array(str_replace($this->_getScanPath().'/', '', $path), $this->_whitelist[$list])) {
            return true;
        }
        return false;
    }
}
