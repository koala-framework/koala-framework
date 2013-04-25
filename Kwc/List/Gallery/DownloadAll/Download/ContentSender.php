<?php
class Kwc_List_Gallery_DownloadAll_Download_ContentSender extends Kwf_Component_Abstract_ContentSender_Download
{
    public function sendDownload()
    {
        $galleryComponent = $this->_data->parent->parent;
        $components = $galleryComponent->getChildComponents(array('generator' => 'child'));
        $paths = array();
        foreach ($components as $component) {
            $data = $component->getComponent()->getImageData();
            if ($data['file']) {
                $paths[$data['filename']] = $data['file'];
            }
        }
        if (!$paths) {
            throw new Kwf_Exception_NotFound();
        }
        // TODO: caching
        Kwf_Util_TempCleaner::clean();
        $tmpname = "temp/" . uniqid() . ".zip";
        $zip = new ZipArchive();
        if ($zip->open($tmpname, ZIPARCHIVE::CREATE)!==TRUE) {
            throw new Kwf_Exception('Could not open file for writing: ' . $filename);
        }
        foreach ($paths as $filename => $path) {
            $zip->addFile($path , $filename);
        }
        $zip->close();
        $file = array(
            'contents' => file_get_contents($tmpname),
            'mimeType' => 'application/zip',
            'downloadFilename' => $galleryComponent->getPage()->name . '.zip'
        );
        Kwf_Media_Output::output($file);
    }
}
