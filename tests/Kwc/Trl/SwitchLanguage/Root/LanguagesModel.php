<?php
class Kwc_Trl_SwitchLanguage_Root_LanguagesModel extends Kwc_Trl_RootModel
{
    public function __construct(array $values = array())
    {
        parent::__construct(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));
    }
}

