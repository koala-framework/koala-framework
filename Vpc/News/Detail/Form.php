<?php
class Vpc_News_Detail_Form extends Vpc_News_Detail_Abstract_Form
{
    public function __construct($newsClass = null, $id = null)
    {
        parent::__construct($newsClass, $id);

        $c = Vpc_Abstract::getSetting($newsClass, 'childComponentClasses');
        $detailClasses = Vpc_Abstract::getSetting($c['detail'], 'childComponentClasses');

        $this->add(Vpc_Abstract_Form::createComponentForm('image', $detailClasses['image']))
            ->setIdTemplate('news_{0}-image');

//  1:1 Form:
//         $this->_form->add(new Vps_Form())
//             ->setTable(new ...Events)
//             ->setIdTemplate('{id}')
    }
}
