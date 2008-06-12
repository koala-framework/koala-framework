<?php
class Vpc_News_Detail_Form extends Vpc_News_Detail_Abstract_Form
{
    public function __construct($detailClass = null, $id = null)
    {
        parent::__construct($detailClass, $id);

        $detailClasses = Vpc_Abstract::getSetting($detailClass, 'childComponentClasses');

        $this->add(Vpc_Abstract_Form::createComponentForm('image', $detailClasses['image']))
            ->setIdTemplate('news_{0}-image');

//  1:1 Form:
//         $this->_form->add(new Vps_Form())
//             ->setTable(new ...Events)
//             ->setIdTemplate('{id}')
    }
}
