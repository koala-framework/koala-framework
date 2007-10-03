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
        $uploadId = $this->_getParam('uploadId');
        $this->_showFile($this->_getSourcePath($uploadId));
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

        // Direkt auf Originaldatei springen
        $password = Vps_Media_Password::get(Vps_Media_Password::ORIGINAL);
        if ($checksum == $this->_createChecksum($password)) {
            $uploadId = $this->_getParam('uploadId');
            $this->_showFile($this->_getSourcePath($uploadId));
            return;
        }

        // Cache
        $password = Vps_Media_Password::get();
        if ($checksum == $this->_createChecksum($password)) {
            $this->cacheAction();
            return;
        }

        throw new Vps_Controller_Action_Web_Exception('Access to file not allowed.');
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

        $target = $this->_getCachePath($uploadId, $this->_getCacheFilename());
        if (!is_file($target)) {

            // Verzeichnisse anlegen, falls nicht existent
            if (!is_dir($this->_getUploadDir() . 'cache/')) {
                mkdir($this->_getUploadDir() . 'cache/', 0775);
                chmod($this->_getUploadDir() . 'cache/', 0775);
            }
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0775);
                chmod(dirname($target), 0775);
            }

            // Cache-Datei erstellen
            $source = $this->_getSourcePath($uploadId);
            try {
                $this->_createCacheFile($source, $target);
            } catch (Exception $e) {
                throw new Vps_Controller_Action_Web_Exception($e->getMessage()); // immer 404 auswerfen
            }

            // Aufräumen, falls Verzeichnis angelegt wurde und keine Datei erstellt wurde, wieder löschen
            if (sizeof(scandir(dirname($target))) <= 2) {
                @rmdir(dirname($target));
            }
        }

        $this->_showFile($target);
    }

    // Überschreiben

    /**
     * Erstellt die Prüfsumme für die passwordAction.
     *
     * @param string Passwort für die Verwendung in der Prüfsumme
     * @return Prüfsumme als string
     */
    protected function _createChecksum($password)
    {
        return md5($password . $this->_getParam('uploadId'));
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
        $source = $this->_getSourcePath($uploadId);
        $extension = strrchr($source, '.');
        return $this->_getUploadDir() . 'cache/' . $uploadId . '/' . $filename . $extension;
    }

    protected final function _getSourcePath($uploadId)
    {
        $table = new Vps_Dao_File();
        $row = $table->find($uploadId)->current();
        if ($row) {
            return $this->_getUploadDir() . $row->path;
        }
        return '';
    }

    protected final function _getUploadDir()
    {
        $config = Zend_Registry::get('config');
        return $config->uploads;
    }

    protected final function _showFile($target)
    {
        if (is_file($target)) {
            $extension = substr(strrchr($target, '.'), 1);
            switch ($extension) {
                case "pdf": $ctype="application/pdf"; break;
                case "zip": $ctype="application/zip"; break;
                case "doc": $ctype="application/msword"; break;
                case "xls": $ctype="application/vnd.ms-excel"; break;
                case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpeg": case "jpg": $ctype="image/jpg"; break;
                case "mp3": $ctype="audio/mpeg"; break;
                case "wav": $ctype="audio/x-wav"; break;
                case "mpeg": case "mpg": case "mpe": $ctype="video/mpeg"; break;
                case "mov": $ctype="video/quicktime"; break;
                case "avi": $ctype="video/x-msvideo"; break;
                default: $ctype="application/octet-stream"; break;
            }
            Zend_Controller_Action_HelperBroker::removeHelper('ViewRenderer');
            chmod($target, 0664);
            $response = $this->getResponse();
            $response->setHeader("Content-type", $ctype);
            $response->setBody(file_get_contents($target));
        } else {
            throw new Vps_Controller_Action_Web_Exception('File not found.');
        }
    }

}