<?php
class Kwc_Advanced_VideoPlayer_Form extends Kwc_Abstract_Composite_Form
{
    protected $_model = 'Kwc_Advanced_VideoPlayer_Model';

    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
//         $this->setXtype('Kwc.Basic.DownloadTag');
        $this->fields->add(new Kwf_Form_Field_File('FileMp4', trlKwf('File (mp4)')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);
        $this->fields->add(new Kwf_Form_Field_TextField('mp4_filename', trlKwf('Filename (mp4)')))
            ->setVtype('alphanum')
            ->setAutoFillWithFilename('mp4_filename') //to find it in MultiFileUpload and javascript
            ->setHelpText(hlpKwf('kwf_download_filename'));

        $this->fields->add(new Kwf_Form_Field_File('FileWebm', trlKwf('File (webm)')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);
        $this->fields->add(new Kwf_Form_Field_TextField('webm_filename', trlKwf('Filename (webm)')))
            ->setVtype('alphanum')
            ->setAutoFillWithFilename('webm_filename') //to find it in MultiFileUpload and javascript
            ->setHelpText(hlpKwf('kwf_download_filename'));

        $this->fields->add(new Kwf_Form_Field_File('FileOgg', trlKwf('File (ogv)')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);
        $this->fields->add(new Kwf_Form_Field_TextField('ogg_filename', trlKwf('Filename (ogg)')))
            ->setVtype('alphanum')
            ->setAutoFillWithFilename('ogg_filename') //to find it in MultiFileUpload and javascript
            ->setHelpText(hlpKwf('kwf_download_filename'));
    }
}
