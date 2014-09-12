<?php
class Kwc_Advanced_CommunityVideo_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);
        $this->fields->add(new Kwf_Form_Field_Static(trlKwfStatic('Please insert the web address to your YouTube or Vimeo video.')))
            ->setWidth(400);
        $this->fields->add(new Kwf_Form_Field_TextField('url', trlKwfStatic('URL')))
            ->setWidth(400)
            ->setVtype('url');

        $cards = new Kwf_Form_Container_Cards('size', trlKwfStatic('Size'));
        $cards->setDefaultValue('fullWidth');
        $cards->setAllowBlank(false);

        $card = $cards->add();
        $card->setTitle(trlKwfStatic('full width'));
        $card->setName('fullWidth');

        $card = $cards->add();
        $card->setTitle(trlKwfStatic('user-defined'));
        $card->setName('custom');
        $card->add(new Kwf_Form_Field_TextField('width', trlKwfStatic('Width')))
            ->setAllowBlank(false);
        $card->add(new Kwf_Form_Field_TextField('height', trlKwfStatic('Height')))
            ->setAllowBlank(false);

        $this->add($cards);

        $this->add(new Kwf_Form_Field_Select('dimensions', trlKwfStatic('Dimension')))
            ->setValues(array(
                '16x9' => trlKwfStatic('16:9'),
                '4x3' => trlKwfStatic('4:3')
            ));

        $this->fields->add(new Kwf_Form_Field_Checkbox('show_similar_videos', trlKwfStatic('Show similar videos (YouTube only)')));
        $this->fields->add(new Kwf_Form_Field_Checkbox('autoplay', trlKwfStatic('Autoplay Video')));
    }
}
