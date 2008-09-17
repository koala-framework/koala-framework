<?php
class Vps_Controller_Action_Media_UploadController extends Vps_Controller_Action
{
    public function jsonUploadAction()
    {
        if (!isset($_FILES['Filedata'])) {
            throw new Vps_Exception("No Filedata received");
        }
        $file = $_FILES['Filedata'];
        if ($file['error']) {
            if ($file['error'] == UPLOAD_ERR_NO_FILE) {
                throw new Vps_Exception("No File uploaded");
            } else {
                throw new Vps_Exception("Upload error $file[error]");
            }
        }
        if (!isset($file['tmp_name']) || !is_file($file['tmp_name'])) {
            throw new Vps_Exception("No File found");
        }
        $t = new Vps_Dao_File();
        $fileRow = $t->createRow();
        $fileRow->uploadFile($file);

        $this->view->value = $fileRow->getFileInfo();
    }

    public function previewAction()
    {
        //TODO: mit hash absichern!!!
        $t = new Vps_Dao_File();
        $fileRow = $t->find($this->_getParam('uploadId'))->current();
        if (!$fileRow) throw new Vps_Exception("Can't find upload");

        $sizes = array(
            'default' => array(40, 40),
            'frontend' => array(100, 100, Vps_Media_Image::SCALE_CROP),
            'gridRow' => array(40, 20, Vps_Media_Image::SCALE_CROP),
            'gridRowLarge' => array(200, 200, Vps_Media_Image::SCALE_BESTFIT),
        );
        if (isset($sizes[$this->_getParam('size')])) {
            $size = $this->_getParam('size');
        } else {
            $size = 'default';
        }

        $uploadDir = Vps_Dao_Row_File::getUploadDir();
        $uploadId = $fileRow->id;
        $target = "$uploadDir/cache/$uploadId/preview$size";
        if (!is_file($target) && !is_link($target)) {
            // Verzeichnisse anlegen, falls nicht existent
            Vps_Dao_Row_File::prepareCacheTarget($target);

            // Cache-Datei erstellen
            $source = $fileRow->getFileSource();
            if (!Vps_Media_Image::scale($source, $target, $sizes[$size])) {
                throw new Vps_Controller_Action_Web_Exception('Invalid Image');
            }
        }
        Vps_Media_Output::output(array(
            'file' => $target,
            'mimeType' => $fileRow->mime_type
        ));
    }

    public function downloadAction()
    {
        //TODO: mit hash absichern?
        $t = new Vps_Dao_File();
        $fileRow = $t->find($this->_getParam('uploadId'))->current();
        if (!$fileRow) throw new Vps_Exception("Can't find upload");

        $source = $fileRow->getFileSource();
        Vps_Media_Output::output(array(
            'file' => $source,
            'mimeType' => $fileRow->mime_type,
            'downloadFilename' => $fileRow->filename . '.' . $fileRow->extension
        ));
    }
}
