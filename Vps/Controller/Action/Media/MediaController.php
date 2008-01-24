<?php
/**
 * Liefert Dateien aus einem zentralen Verzeichnis aus.
 *
 * Voraussetzungen für die Verwendung:
 *
 * Das Verzeichnis, in dem die Dateien liegen, muss in der config-Datei unter
 * dem Wert "uploads" eingetragen sein.
 *
 * Die Tabelle vps_uploads muss existieren.
 *
 * Der Controller muss abgeleitet und die Routen entsprechend eingerichtet
 * werden. Der Controller bietet drei Actions an: original, password, cache.
 * Alle Actions sind auf den Parameter 'uploadId' angewiesen. Im Zuge dessen
 * sollte auch die Zugangsberechtigungen gesetzt werden.
 */
class Vps_Controller_Action_Media_MediaController extends Vps_Controller_Action
{
    public function passwordAction()
    {
        $checksum = md5(
            Vps_Db_Table_Row::FILE_PASSWORD .
            $this->_getParam('table') .
            $this->_getParam('id') .
            $this->_getParam('rule') .
            $this->_getParam('type')
        );
        if ($checksum != $this->_getParam('checksum')) {
            throw new Vps_Controller_Action_Web_Exception('Access to file not allowed.');
        }

        $class = $this->_getParam('table');
        $type = $this->_getParam('type');
        $id = explode(',', $this->_getParam('id'));
        $rule = $this->_getParam('rule');
        if ($rule == 'default') { $rule = null; }

        if (substr($class, 0, 4) == 'Vpc_') {
            $tableClass = Vpc_Abstract::getSetting($class, 'tablename');
            $table = new $tableClass(array('componentClass' => $class));
        } else {
            $table = new $class();
        }
        $row = call_user_func_array(array($table, 'find'), $id)->current();
        if (!$row) {
            throw new Vps_Exception('File not found.');
        }
        $fileRow = $row->findParentRow('Vps_Dao_File', $rule);
        if (!$fileRow) {
            throw new Vps_Exception('No File uploaded.');
        }
        $target = $row->getFileSource($rule, $type);

        $this->_showFile($target, $fileRow);
    }

    protected final function _showFile($target, Vps_Dao_Row_File $row)
    {
        if (is_file($target)) {
            $response = $this->getResponse();
            $response->setHeader("Content-type", $row->mime_type);
            $response->setBody(file_get_contents($target));
            $this->_helper->viewRenderer->setNoRender();
        } else {
            throw new Vps_Controller_Action_Web_Exception("File '$target' not found.");
        }
    }

}
