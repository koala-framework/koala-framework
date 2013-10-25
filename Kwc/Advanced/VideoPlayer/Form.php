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

        $fs = $this->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Settings')))
            ->setHelpText(trlKwf('Insert "100%" in both fields to make it responsive.'));

        $cards = $fs->add(new Kwf_Form_Container_Cards('size_type', trlKwf('Size')));
        $cards->getCombobox()
            ->setWidth(300)
            ->setListWidth(300);

        $card = $cards->add(new Kwf_Form_Container_Card('contentWidth'))
            ->setTitle(trlKwf('Stretch video to maximum width'));
        $card->add(new Kwf_Form_Field_Select('format', trlKwf('Format')))
            ->setValues(array('16x9' => trlKwfStatic('16:9'), '4x3' => trlKwfStatic('4:3')))
            ->setDefaultValue('16x9')
            ->setAllowBlank(false);

        $card = $cards->add(new Kwf_Form_Container_Card('userDefined'))
            ->setTitle(trlKwf('Set size of video'));
        $card->add(new Kwf_Form_Field_TextField('video_width', trlKwf('Width (px)')))
            ->setWidth(80);
        $card->add(new Kwf_Form_Field_TextField('video_height', trlKwf('Height (px)')))
            ->setWidth(80);

        $fs->add(new Kwf_Form_Field_Checkbox('loop', trlKwf('Repeat')));
        $fs->add(new Kwf_Form_Field_Checkbox('auto_play', trlKwf('Auto play')));
    }
}
