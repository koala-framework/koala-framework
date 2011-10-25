<?php
class Kwc_Advanced_CommunityVideo_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Kwf_Form_Field_Static(trlKwf('Please insert the web address to your YouTube or Vimeo video.')))
            ->setWidth(400);
        $this->fields->add(new Kwf_Form_Field_TextField('url', trlKwf('URL')))
            ->setWidth(400)
            ->setVtype('url');
        $this->fields->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')))
            ->setMinValue(1)
            ->setMaxValue(9999);
        $this->fields->add(new Kwf_Form_Field_NumberField('height', trlKwf('Height')))
            ->setMinValue(1)
            ->setMaxValue(9999);
        $this->fields->add(new Kwf_Form_Field_Checkbox('show_similar_videos', trlKwf('Show similar videos (YouTube only)')));
    }
}
