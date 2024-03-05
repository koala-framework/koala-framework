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
            $politeM = $trl->trlKwf('Dear Mr. {0} {1}', $replace, $language);
            $politeF = $trl->trlKwf('Dear Mrs. {0} {1}', $replace, $language);
            if ($recipient->getMailGender() == 'male' && $recipient->getMailLastname()) {
                $t = $trl->trlKwf('Dear Mr. {0} {1}', $replace, $language);
            } else if ($recipient->getMailGender() == 'female' && $recipient->getMailLastname()) {
                $t = $trl->trlKwf('Dear Mrs. {0} {1}', $replace, $language);
            } else {
                $t = $trl->trlKwf('Dear Mrs./Mr. {0} {1}', $replace, $language);
            }
            $ret['salutation_polite'] = trim(str_replace('  ', ' ', $t));

            if ($recipient->getMailGender() == 'male') {
                $t = $trl->trlKwf('Mr. {0}', $recipient->getMailTitle(), $language);
            } else if ($recipient->getMailGender() == 'female') {
                $t = $trl->trlKwf('Mrs. {0}', $recipient->getMailTitle(), $language);
            } else {
                $t = $recipient->getMailTitle();
            }
            $ret['salutation_title'] = trim(str_replace('  ', ' ', $t));

            $ret['title'] = $recipient->getMailTitle();
        }
        if ($recipient instanceof Kwc_Mail_Recipient_GenderInterface) {
            $replace = array($recipient->getMailLastname());
            if ($recipient->getMailGender() == 'male') {
                $ret['salutation_polite_notitle'] = $trl->trlKwf('Dear Mr. {0}', $replace, $language);
                $ret['salutation_hello'] = $trl->trlKwf('Hello Mr. {0}', $replace, $language);
                $ret['salutation'] = $trl->trlKwf('Mr.', array(), $language);
                $ret['salutation_firstname'] = $trl->trlcKwf('salutation firstname male', 'Dear {0}', array($recipient->getMailFirstname()), $language);
            } else if ($recipient->getMailGender() == 'female') {
                $ret['salutation_polite_notitle'] = $trl->trlKwf('Dear Mrs. {0}', $replace, $language);
                $ret['salutation_hello'] = $trl->trlKwf('Hello Mrs. {0}', $replace, $language);
                $ret['salutation'] = $trl->trlKwf('Mrs.', array(), $language);
                $ret['salutation_firstname'] = $trl->trlcKwf('salutation firstname female', 'Dear {0}', array($recipient->getMailFirstname()), $language);
            } else {
                $replace = array(
                    $recipient->getMailFirstname(),
                    $recipient->getMailLastname()
                );
                if ($recipient->getMailFirstname() && $recipient->getMailLastname()) {
                    $ret['salutation_polite_notitle'] = trim($trl->trlKwf('Dear {0} {1}', $replace, $language));
                } else {
                    $ret['salutation_polite_notitle'] = $trl->trlKwf('Dear Sir or Madam', array(), $language);
                }
                $ret['salutation_hello'] = trim($trl->trlKwf('Hello {0} {1}', $replace, $language));
                $ret['salutation_firstname'] = $trl->trlcKwf('salutation firstname unknown gender', 'Dear {0}', array($recipient->getMailFirstname()), $language);
                $ret['salutation'] = '';
            }
        }
        return $ret;
    }
}
