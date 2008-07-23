<?php
class Vpc_News_Detail_Form extends Vpc_News_Detail_Abstract_Form
{
    public function __construct($newsClass = null, $id = null)
    {
        parent::__construct($newsClass, $id);

        $this->add(Vpc_Abstract_Form::createComponentForm('news_{0}-image'));

//  1:1 Form:
//         $this->_form->add(new Vps_Form())
//             ->setTable(new ...Events)
//             ->setIdTemplate('{id}')
    }
}
