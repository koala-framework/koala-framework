<?php
class Vpc_Mail_PlaceholdersPlugin extends Vps_Component_Plugin_Placeholders
{
    public function processMailOutput($text, Vpc_Mail_Recipient_Interface $recipient = null)
    {
        // gender
        $pattern = '/\%(.*)\:(.*)\%/U';
        if ($recipient->getMailGender() == Vpc_Mail_Recipient_Interface::MAIL_GENDER_MALE) {
            $text = preg_replace($pattern, '$1', $text);
            $gender = trlVps('Mr.');
        } else {
            $text = preg_replace($pattern, '$2', $text);
            $gender = trlVps('Ms.');
        }
        $text = str_replace('%gender%', $gender, $text);
        // title
        $title = $recipient->getMailTitle();
        $search = $title == '' ? '%title% ' : '%title%';
        $text = str_replace($search, $title, $text);
        // firstname
        $firstname = $recipient->getMailFirstname();
        $search = $firstname == '' ? '%firstname% ' : '%firstname%';
        $text = str_replace($search, $firstname, $text);
        // lastname
        $text = str_replace('%lastname%', $recipient->getMailLastname(), $text);
        return $text;
    }
}
