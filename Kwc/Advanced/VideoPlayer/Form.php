<?php
class Kwc_Advanced_VideoPlayer_Form extends Kwc_Abstract_Composite_Form
{
    protected $_model = 'Kwc_Advanced_VideoPlayer_Model';

    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Kwf_Form_Field_File('FileMp4', trlKwf('File (mp4)')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);

        $this->fields->add(new Kwf_Form_Field_File('FileWebm', trlKwf('File (webm)')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);

        $this->fields->add(new Kwf_Form_Field_File('FileOgg', trlKwf('File (ogv)')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);
    }
}
