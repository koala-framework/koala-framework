<?php
class Kwc_Mail_Recipient_Placeholders
{
    /*
     * Returns Placeholders with different salutations
     *
     * $recipient contains recipient row
     * $language contains in which lanuage the Placeholders should be translated
     */
    public static function getPlaceholders(Kwc_Mail_Recipient_Interface $recipient, $language)
    {
        $ret = array();
        $trl = Kwf_Trl::getInstance();
        $ret['firstname'] = $recipient->getMailFirstname();
        $ret['lastname'] = $recipient->getMailLastname();
        if ($recipient instanceof Kwc_Mail_Recipient_TitleInterface) {
            $replace = array(
                $recipient->getMailTitle(),
                $recipient->getMailLastname()
            );
            $politeM = $trl->trl('Dear Mr. {0} {1}', $replace, Kwf_Trl::SOURCE_KWF, $language);
            $politeF = $trl->trl('Dear Mrs. {0} {1}', $replace, Kwf_Trl::SOURCE_KWF, $language);
            if ($recipient->getMailGender() == 'male' && $recipient->getMailLastname()) {
                $t = $trl->trl('Dear Mr. {0} {1}', $replace, Kwf_Trl::SOURCE_KWF, $language);
            } else if ($recipient->getMailGender() == 'female' && $recipient->getMailLastname()) {
                $t = $trl->trl('Dear Mrs. {0} {1}', $replace, Kwf_Trl::SOURCE_KWF, $language);
            } else {
                $t = $trl->trl('Dear Sir or Madam', array(), Kwf_Trl::SOURCE_KWF, $language);
            }
            $ret['salutation_polite'] = trim(str_replace('  ', ' ', $t));

            if ($recipient->getMailGender() == 'male') {
                $t = $trl->trl('Mr. {0}', $recipient->getMailTitle(), Kwf_Trl::SOURCE_KWF, $language);
            } else if ($recipient->getMailGender() == 'female') {
                $t = $trl->trl('Mrs. {0}', $recipient->getMailTitle(), Kwf_Trl::SOURCE_KWF, $language);
            } else {
                $t = $recipient->getMailTitle();
            }
            $ret['salutation_title'] = trim(str_replace('  ', ' ', $t));

            $ret['title'] = $recipient->getMailTitle();
        }
        if ($recipient instanceof Kwc_Mail_Recipient_GenderInterface) {
            $replace = array($recipient->getMailLastname());
            if ($recipient->getMailGender() == 'male') {
                $ret['salutation_polite_notitle'] = $trl->trl('Dear Mr. {0}', $replace, Kwf_Trl::SOURCE_KWF, $language);
                $ret['salutation_hello'] = $trl->trl('Hello Mr. {0}', $replace, Kwf_Trl::SOURCE_KWF, $language);
                $ret['salutation'] = $trl->trl('Mr.', array(), Kwf_Trl::SOURCE_KWF, $language);
                $ret['salutation_firstname'] = $trl->trlc('salutation firstname male', 'Dear {0}', array($recipient->getMailFirstname()), Kwf_Trl::SOURCE_KWF, $language);
            } else if ($recipient->getMailGender() == 'female') {
                $ret['salutation_polite_notitle'] = $trl->trl('Dear Mrs. {0}', $replace, Kwf_Trl::SOURCE_KWF, $language);
                $ret['salutation_hello'] = $trl->trl('Hello Mrs. {0}', $replace, Kwf_Trl::SOURCE_KWF, $language);
                $ret['salutation'] = $trl->trl('Mrs.', array(), Kwf_Trl::SOURCE_KWF, $language);
                $ret['salutation_firstname'] = $trl->trlc('salutation firstname female', 'Dear {0}', array($recipient->getMailFirstname()), Kwf_Trl::SOURCE_KWF, $language);
            } else {
                $replace = array(
                    $recipient->getMailFirstname(),
                    $recipient->getMailLastname()
                );
                if ($recipient->getMailFirstname() && $recipient->getMailLastname()) {
                    $ret['salutation_polite_notitle'] = trim($trl->trl('Dear {0} {1}', $replace, Kwf_Trl::SOURCE_KWF, $language));
                } else {
                    $ret['salutation_polite_notitle'] = $trl->trl('Dear Sir or Madam', array(), Kwf_Trl::SOURCE_KWF, $language);
                }
                $ret['salutation_hello'] = trim($trl->trl('Hello {0} {1}', $replace, Kwf_Trl::SOURCE_KWF, $language));
                $ret['salutation_firstname'] = $trl->trlc('salutation firstname unknown gender', 'Dear {0}', array($recipient->getMailFirstname()), Kwf_Trl::SOURCE_KWF, $language);
            }
        }
        return $ret;
    }
}
