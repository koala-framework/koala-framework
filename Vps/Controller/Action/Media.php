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
class Vps_Controller_Action_Media extends Vps_Controller_Action
{
    /**
     * Die angeforderte Datei wird ohne Änderung ausgeliefert.
     */
    public function originalAction()
    {
        $row = $this->_getRow($this->_getParam('uploadId'));
        $this->_showFile($row->getFileSource(), $row);
    }

    /**
     * Die angeforderte Datei wird ausgeliefert, wenn die Prüfsumme passt.
     *
     * Die Passwörter sind in der Klasse Vps_Media_Password gespeichert und
     * über die statische Methode get() zu holen. Die Methode
     * _createChecksum() kann für die Überprüfung überschrieben werden.
     *
     * Es wird entweder die originale oder eine gecachte Datei ausgeliefert.
     * Dies wird durch das Passwort unterschieden.
     *
     * Diese Action benötigt zusätzlich den Parameter 'checksum'.
     *
     * @see Vps_Media_Password
     * @see _createChecksum()
     */
    public function passwordAction()
    {
        $checksum = $this->_getParam('checksum');
        $type = $this->_getParam('type');
        if ($checksum != $this->_createChecksum()) {
            throw new Vps_Controller_Action_Web_Exception('Access to file not allowed.');
        }

        if ($type == 'original') {
            // Direkt auf Originaldatei springen
            $this->originalAction();
        } else {
            $this->cacheAction();
        }
    }

    /**
     * Eine gecachte Datei wird ausgeliefert.
     *
     * Der Dateiname der gecachten Datei kann durch Überschreiben von
     * $this->_getCacheFilename() geändert werden (falls es zB. zwei
     * gecachte Dateien für eine Originaldatei gibt).
     * Die Verzeichnisse für den Cache werden automatisch angelegt
     * und ggf. gelöscht.
     *
     * Das Erstellen der Cache-Datei sollte durch Überschreiben von
     * $this->createCacheFile() geschehen.
     */
    public function cacheAction()
    {
        $uploadId = $this->_getParam('uploadId');
        $row = $this->_getRow($uploadId);
        if (!$row) {
            throw new Vps_Controller_Action_Web_Exception('File not found');
        }

        $target = $this->_getCachePath($uploadId, $this->_getCacheFilename());
        if (!is_file($target)) {
            // Verzeichnisse anlegen, falls nicht existent
            if (!is_dir($this->_getUploadDir() . '/cache')) {
                mkdir($this->_getUploadDir() . '/cache', 0775);
                chmod($this->_getUploadDir() . '/cache', 0775);
            }
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0775);
                chmod(dirname($target), 0775);
            }

            // Cache-Datei erstellen
            $source = $row->getFileSource();
            try {
                $this->_createCacheFile($source, $target, $this->_getParam('type'));
            } catch (Exception $e) {
                throw new Vps_Controller_Action_Web_Exception($e->getMessage()); // immer 404 auswerfen
            }

            // Aufräumen, falls Verzeichnis angelegt wurde und keine Datei erstellt wurde, wieder löschen
            if (sizeof(scandir(dirname($target))) <= 2) {
                @rmdir(dirname($target));
            }
        }

        $this->_showFile($target, $row);
    }

    // Überschreiben

    /**
     * Erstellt die Prüfsumme für die passwordAction.
     *
     * @param string Passwort für die Verwendung in der Prüfsumme
     * @return Prüfsumme als string
     */
    protected function _createChecksum()
    {
        return md5(Vps_Media_Password::PASSWORD .
                    $this->_getParam('uploadId'));
    }

    /**
     * Bestimmt den Dateinamen der gecachten Datei.
     *
     * @return Dateiname (ohne Endung!)
     */
    protected function _getCacheFilename()
    {
        return $this->_getParam('uploadId');
    }

    /**
     * Erstellt die gecachte Datei.
     *
     * Hier können beispielsweise skalierte Bilder erzeugt werden.
     *
     * @param string Der Pfad zur Originaldatei
     * @param string Der Pfad zur gecachten Datei
     */
    protected function _createCacheFile($source, $target)
    {
        copy($source, $target);
    }

    // Ab hier Final
    protected final function _getCachePath($uploadId, $filename)
    {
        return $this->_getUploadDir() . '/cache/' . $uploadId . '/' . $filename;
    }

    protected final function _getRow($uploadId)
    {
        $table = new Vps_Dao_File();
        $row = $table->find($uploadId)->current();
        if ($row) {
            return $row;
        }
        return '';
    }

    protected final function _getUploadDir()
    {
        $config = Zend_Registry::get('config');
        return $config->uploads;
    }

    protected final function _showFile($target, Vps_Dao_Row_File $row)
    {
        if (is_file($target)) {
            Zend_Controller_Action_HelperBroker::removeHelper('ViewRenderer');
            chmod($target, 0664);
            $response = $this->getResponse();
            $response->setHeader("Content-type", $row->mime_type);
            $response->setBody(file_get_contents($target));
        } else {
            throw new Vps_Controller_Action_Web_Exception("File '$target' not found.");
        }
    }

}
