<?php
class Kwc_Advanced_VideoPlayer_Form extends Kwc_Abstract_Composite_Form
{
    protected $_model = 'Kwc_Advanced_VideoPlayer_Model';

    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $fs = $this->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Files')));
        $fs->add(new Kwf_Form_Field_File('FileMp4', trlKwf('Mp4 file')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);

        $fs->add(new Kwf_Form_Field_File('FileOgg', trlKwf('Ogg File')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);

        $fs->add(new Kwf_Form_Field_File('FileWebm', trlKwf('Webm file (optional)')))
            ->setDirectory('AdvancedVideoPlayer')
            ->setAllowOnlyImages(false);

        $fs = $this->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Settings')));
        $fs->add(new Kwf_Form_Field_TextField('video_width', trlKwf('Width (px)')))
            ->setWidth(80);
        $fs->add(new Kwf_Form_Field_TextField('video_height', trlKwf('Height (px)')))
            ->setWidth(80);
        $fs->add(new Kwf_Form_Field_Checkbox('loop', trlKwf('Repeat')));
        $fs->add(new Kwf_Form_Field_Checkbox('auto_play', trlKwf('Auto play')));
    }
}
