<?php
class Vps_Dao_Row_File extends Vps_Db_Table_Row_Abstract
{
    public static function getUploadDir()
    {
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;

        if (!$uploadDir) {
            throw new Vps_Exception(trlVps('Param "uploads" has to be set in the file application/config.ini.'));
        }
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            throw new Vps_Exception(trlVps('Path for uploads is not writeable: {0}', $uploadDir));
        }

        return $uploadDir;
    }

    //hilfsfkt wird vor erstellen des caches aufgerufen damit die ordner korrekt
    //erstellt werden. passt nicht wirklich hier her.
    public static function prepareCacheTarget($target)
    {
        $uploadDir = Vps_Dao_Row_File::getUploadDir();
        if (!is_dir($uploadDir . '/cache')) {
            mkdir($uploadDir . '/cache', 0775);
            chmod($uploadDir . '/cache', 0775);
        }
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0775);
            chmod(dirname($target), 0775);
        }
    }

    public function getFileInfo()
    {
        $ret = array(
            'uploadId' => $this->id,
            'mimeType' => $this->mime_type,
            'filename' => $this->filename,
            'extension'=> $this->extension,
            'fileSize' => $this->getFileSize()
        );
        if (!$this->id && is_file($this->filename)) {
            $ret['mimeType'] = $this->_getMimeType($this->filename);
            $ret['extension'] = substr(strrchr($this->filename, '.'), 1);
        }
        $size = @getimagesize($this->getFileSource());
        if ($size) {
            $ret['image'] = true;
            $ret['imageWidth'] = $size[0];
            $ret['imageHeight'] = $size[1];
        } else {
            $ret['image'] = false;
        }
        return $ret;
    }

    public function getFileSource()
    {
        if (!$this->id) {
            if ($this->filename && is_file($this->filename)) {
                return $this->filename;
            } else {
                return null;
            }
        }
        return self::getUploadDir() . '/' . $this->id;
    }

    public function getFileSize()
    {
        $file = $this->getFileSource();
        if ($file && is_file($file)) {
            return filesize($file);
        }
        return null;
    }

    public function deleteFile()
    {
        $filename = $this->getFileSource();
        if ($filename && is_file($filename)) {
            unlink($filename);
        }
        $this->deleteCache();
    }

    public function deleteCache()
    {
        if ($this->id) {
            $this->_recursiveRemoveDirectory(self::getUploadDir() . '/cache/' . $this->id);
        }
    }

    private function _recursiveRemoveDirectory($dir)
    {
        if (!file_exists($dir) || !is_dir($dir)) return;
        $iterator = new RecursiveDirectoryIterator($dir);
        foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($dir);
    }

    //wird von Zend_Db_Table_Row_Abstract vorm löschen aufgerufen
    protected function _delete()
    {
        $this->deleteFile();
    }

    private function _getMimeType($filename) 
    { 
       $mime = array( 
            ".htm" =>"application/xhtml+xml",
            ".3dm" =>"x-world/x-3dmf",
            ".3dmf" =>"x-world/x-3dmf",
            ".ai" =>"application/postscript",
            ".aif" =>"audio/x-aiff",
            ".aifc" =>"audio/x-aiff",
            ".aiff" =>"audio/x-aiff",
            ".au" =>"audio/basic",
            ".avi" =>"video/x-msvideo",
            ".bcpio" =>"application/x-bcpio",
            ".bin" =>"application/octet-stream",
            ".cab" =>"application/x-shockwave-flash",
            ".cdf" =>"application/x-netcdf",
            ".chm" =>"application/mshelp",
            ".cht" =>"audio/x-dspeeh",
            ".class" =>"application/octet-stream",
            ".cod" =>"image/cis-cod",
            ".com" =>"application/octet-stream",
            ".cpio" =>"application/x-cpio",
            ".csh" =>"application/x-csh",
            ".css" =>"text/css",
            ".csv" =>"text/comma-separated-values",
            ".dcr" =>"application/x-director",
            ".dir" =>"application/x-director",
            ".dll" =>"application/octet-stream",
            ".doc" =>"application/msword",
            ".dot" =>"application/msword",
            ".dus" =>"audio/x-dspeeh",
            ".dvi" =>"application/x-dvi",
            ".dwf" =>"drawing/x-dwf",
            ".dwg" =>"application/acad",
            ".dxf" =>"application/dxf",
            ".dxr" =>"application/x-director",
            ".eps" =>"application/postscript",
            ".es" =>"audio/echospeech",
            ".etx" =>"text/x-setext",
            ".evy" =>"application/x-envoy",
            ".exe" =>"application/octet-stream",
            ".fh4" =>"image/x-freehand",
            ".fh5" =>"image/x-freehand",
            ".fhc" =>"image/x-freehand",
            ".fif" =>"image/fif",
            ".gif" =>"image/gif",
            ".gtar" =>"application/x-gtar",
            ".gz" =>"application/gzip",
            ".hdf" =>"application/x-hdf",
            ".hlp" =>"application/mshelp",
            ".hqx" =>"application/mac-binhex40",
            ".htm" =>"text/html",
            ".html" =>"text/html",
            ".ief" =>"image/ief",
            ".jpeg" =>"image/jpeg",
            ".jpe" =>"image/jpeg",
            ".jpg" =>"image/jpeg",
            ".js" =>"text/javascript",
            ".latex" =>"application/x-latex",
            ".man" =>"application/x-troff-man",
            ".mbd" =>"application/mbedlet",
            ".mcf" =>"image/vasa",
            ".me" =>"application/x-troff-me",
            ".mid" =>"audio/x-midi",
            ".midi" =>"audio/x-midi",
            ".mif" =>"application/mif",
            ".mov" =>"video/quicktime",
            ".movie" =>"video/x-sgi-movie",
            ".mp2" =>"audio/x-mpeg",
            ".mpe" =>"video/mpeg",
            ".mpeg" =>"video/mpeg",
            ".mpg" =>"video/mpeg",
            ".nc" =>"application/x-netcdf",
            ".nsc" =>"application/x-nschat",
            ".oda" =>"application/oda",
            ".pbm" =>"image/x-portable-bitmap",
            ".pdf" =>"application/pdf",
            ".pgm" =>"image/x-portable-graymap",
            ".php" =>"application/x-httpd-php",
            ".phtml" =>"application/x-httpd-php",
            ".png" =>"image/png",
            ".pnm" =>"image/x-portable-anymap",
            ".pot" =>"application/mspowerpoint",
            ".ppm" =>"image/x-portable-pixmap",
            ".pps" =>"application/mspowerpoint",
            ".ppt" =>"application/mspowerpoint",
            ".ppz" =>"application/mspowerpoint",
            ".ps" =>"application/postscript",
            ".ptlk" =>"application/listenup",
            ".qd3" =>"x-world/x-3dmf",
            ".qd3d" =>"x-world/x-3dmf",
            ".qt" =>"video/quicktime",
            ".ram" =>"audio/x-pn-realaudio",
            ".ra" =>"audio/x-pn-realaudio",
            ".ras" =>"image/cmu-raster",
            ".rgb" =>"image/x-rgb",
            ".roff" =>"application/x-troff",
            ".rpm" =>"audio/x-pn-realaudio-plugin",
            ".rtf" =>"application/rtf",
            ".rtf" =>"text/rtf",
            ".rtx" =>"text/richtext",
            ".sca" =>"application/x-supercard",
            ".sgm" =>"text/x-sgml",
            ".sgml" =>"text/x-sgml",
            ".sh" =>"application/x-sh",
            ".shar" =>"application/x-shar",
            ".shtml" =>"text/html",
            ".sit" =>"application/x-stuffit",
            ".smp" =>"application/studiom",
            ".snd" =>"audio/basic",
            ".spc" =>"text/x-speech",
            ".spl" =>"application/futuresplash",
            ".spr" =>"application/x-sprite",
            ".sprite" =>"application/x-sprite",
            ".src" =>"application/x-wais-source",
            ".stream" =>"audio/x-qt-stream",
            ".sv4cpio" =>"application/x-sv4cpio",
            ".sv4crc" =>"application/x-sv4crc",
            ".swf" =>"application/x-shockwave-flash",
            ".t" =>"application/x-troff",
            ".talk" =>"text/x-speech",
            ".tar" =>"application/x-tar",
            ".tbk" =>"application/toolbook",
            ".tcl" =>"application/x-tcl",
            ".tex" =>"application/x-tex",
            ".texinfo" =>"application/x-texinfo",
            ".texi" =>"application/x-texinfo",
            ".tif" =>"image/tiff",
            ".tiff " =>"image/tiff",
            ".trtc" =>"application/rtc",
            ".trtc" =>"application/x-troff",
            ".tsi" =>"audio/tsplayer",
            ".tsp" =>"application/dsptype",
            ".tsv" =>"text/tab-separated-values",
            ".txt" =>"text/plain",
            ".ustar" =>"application/x-ustar",
            ".viv" =>"video/vnd.vivo",
            ".vivo" =>"video/vnd.vivo",
            ".vmd" =>"application/vocaltec-media-desc",
            ".vmf" =>"application/vocaltec-media-file",
            ".vox" =>"audio/voxware",
            ".vts" =>"workbook/formulaone",
            ".vtts" =>"workbook/formulaone",
            ".wav" =>"audio/x-wav",
            ".wbmp" =>"image/vnd.wap.wbmp",
            ".wml" =>"text/vnd.wap.wml",
            ".wmlc" =>"application/vnd.wap.wmlc",
            ".wmls" =>"text/vnd.wap.wmlscript",
            ".wmlsc" =>"application/vnd.wap.wmlscriptc",
            ".wrl" =>"model/vrml",
            ".wrl" =>"x-world/x-vrml",
            ".xbm" =>"image/x-xbitmap",
            ".xhtml" =>"application/xhtml+xml",
            ".xla" =>"application/msexcel",
            ".xls" =>"application/msexcel",
            ".xml" =>"text/xml",
            ".xpm" =>"image/x-xpixmap",
            ".xwd" =>"image/x-windowdump",
            ".z" =>"application/x-compress",
            ".zip" =>"application/zip"
       ); 

       $ext = strrchr($filename, '.');
       return  isset($mime[$ext]) ? $mime[$ext] : 'application/octet-stream'; 
    }  
    
    public function uploadFile($filedata)
    {
        if ($filedata['error'] == UPLOAD_ERR_NO_FILE) {
            throw new Vps_Exception('No File was uploaded.');
        }

        if ($filedata['error'] == UPLOAD_ERR_INI_SIZE || $filedata['error'] == UPLOAD_ERR_FORM_SIZE) {
            throw new Vps_ClientException(trlVps('The file is larger than the maximum upload amount.'));
        }

        if ($filedata['error'] == UPLOAD_ERR_PARTIAL) {
            throw new Vps_ClientException(trlVps('The file was not uploaded completely.'));
        }

        $this->deleteFile();
        $this->filename = substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
        $this->extension = substr(strrchr($filedata['name'], '.'), 1);
        if ($filedata['type'] == 'application/octet-stream') {
            //for flash uploads
            if (function_exists('finfo_open')) {
                //für andere server muss dieser pfad vielleicht einstellbar gemacht werden
                $finfo = new finfo(FILEINFO_MIME, '/usr/share/file/magic');
                $this->mime_type = $finfo->file($filedata['tmp_name']);
            } else if (function_exists('mime_content_type')) {
                $this->mime_type = $this->_getMimeType($filedata['name']);
            } else {
                throw new Vps_Exception("Can't autodetect mimetype");
            }
        } else {
            $this->mime_type = $filedata['type'];
        }
        if (!$this->mime_type) {
            $this->mime_type = 'application/octet-stream';
        }
        $this->save();

        $filename = $this->getFileSource();
        if (move_uploaded_file($filedata['tmp_name'], $filename)) {
            chmod($filename, 0664);
        } else {
            $this->delete();
        }
    }

    public function copyFile($file, $filename, $extension, $mime_type)
    {
        if (!file_exists($file)) {
            throw new Vps_Exception(trlVps("File {0} does not exist", '\''.$file.'\''));
        }
        $this->deleteFile();
        $this->filename = $filename;
        $this->extension = $extension;
        $this->mime_type = $mime_type;
        $this->save();
        copy($file, $this->getFileSource());
        chmod($this->getFileSource(), 0664);
    }

    public function writeFile($contents, $filename, $extension, $mime_type)
    {
        $this->deleteFile();
        $this->filename = $filename;
        $this->extension = $extension;
        $this->mime_type = $mime_type;
        $this->save();
        file_put_contents($this->getFileSource(), $contents);
        chmod($this->getFileSource(), 0664);
    }

    public function duplicate($data = array())
    {
        $new = parent::duplicate($data);
        if (file_exists($this->getFileSource())) {
            copy($this->getFileSource(), $new->getFileSource());
            chmod($new->getFileSource(), 0664);
        }
        return $new;
    }
}
