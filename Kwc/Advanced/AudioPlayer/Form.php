<?php
class Kwc_Advanced_AudioPlayer_Form extends Kwc_Abstract_Composite_Form
{
    protected $_model = 'Kwc_Advanced_AudioPlayer_Model';

    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $fs = $this->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('File')));

        $fs->add(new Kwf_Form_Field_File('FileMp3', trlKwf('Mp3 File')))
            ->setDirectory('AdvancedAudioPlayer')
            ->setAllowOnlyImages(false);

        $fs = $this->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Settings')));
        $fs->add(new Kwf_Form_Field_TextField('audio_width', trlKwf('Width (px)')))
            ->setWidth(80);
        $fs->add(new Kwf_Form_Field_TextField('audio_height', trlKwf('Height (px)')))
            ->setWidth(80);
        $fs->add(new Kwf_Form_Field_Checkbox('loop', trlKwf('Repeat')));
        $fs->add(new Kwf_Form_Field_Checkbox('auto_play', trlKwf('Auto play')));
    }
}
