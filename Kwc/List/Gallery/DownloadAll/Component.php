<?php
/*
 * Add to List_Gallery_Component to show Download All link:
 * $ret['generators']['downloadAll'] = array(
 *   'class' => 'Kwf_Component_Generator_Static',
 *   'component' => 'Kwc_List_Gallery_DownloadAll_Component'
 * );
 *
 */
class Kwc_List_Gallery_DownloadAll_Component extends Kwc_Abstract
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null) {
        $ret = parent::getTemplateVars($renderer);
        $ret['downloadUrl'] = Kwf_Media::getUrl($this->getData()->componentClass,
            $this->getData()->componentId, 'list', 'all'
        );
        return $ret;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValid($id);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type == 'list') {
            $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id);
            if ($component) {
                $galleryComponent = $component->parent;
                $components = $galleryComponent->getChildComponents(array('generator' => 'child'));
                $paths = array();
                foreach ($components as $component) {
                    $data = $component->getComponent()->getImageData();
                    if ($data['file']) {
                        $paths[$data['file']] = $data['filename'];
                    }
                }
            }
            if (!$paths) { throw new Kwf_Exception_NotFound(); }
            Kwf_Util_TempCleaner::clean();
            $tmpname = "temp/" . uniqid() . ".zip";
            $zip = new ZipArchive();
            if ($zip->open($tmpname, ZIPARCHIVE::CREATE)!==TRUE) {
                throw new Kwf_Exception('Could not open file for writing: ' . $filename);
            }
            foreach ($paths as $path => $filename) {
                $zip->addFile($path , $filename);
            }
            $zip->close();
            $file = array(
                'file' => $tmpname,
                'mimeType' => 'application/zip',
                'downloadFilename' => $galleryComponent->getPage()->name . '.zip'
            );
            Kwf_Media_Output::output($file);
            exit;
        }
    }
}
