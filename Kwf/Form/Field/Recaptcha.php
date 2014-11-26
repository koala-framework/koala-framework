<?php
//to use this field add package "koala-framework/recaptcha-php": "1.11"
class Kwf_Form_Field_Recaptcha extends Kwf_Form_Field_Abstract
{
    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);
        $sess = new Kwf_Session_Namespace('recaptcha');
        if ($sess->validated) {
            //if user did solve one captcha we store that in session and don't annoy him again
            return $ret;
        }
        if (empty($_POST["recaptcha_challenge_field"]) || empty($_POST["recaptcha_response_field"])) {
            $ret[] = array(
                'message' => trlKwf('Please solve captcha correctly'),
                'field' => $this
            );
            return $ret;
        }
        require_once('vendor/koala-framework/recaptcha-php/recaptchalib.php');
        $resp = recaptcha_check_answer (Kwf_Config::getValue('recaptcha.privateKey'),
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) {
            $msg = $resp->error;
            if ($msg == 'incorrect-captcha-sol') {
                $msg = trlKwf('Please solve captcha correctly');
            }
            $ret[] = array(
                'message' => $msg,
                'field' => $this
            );
        } else {
            $sess->validated = true;
        }
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        require_once('vendor/koala-framework/recaptcha-php/recaptchalib.php');

        $ret['html'] = "<div data-fieldname=\"".$this->getFieldName().$fieldNamePostfix."\">"
            .recaptcha_get_html(Kwf_Config::getValue('recaptcha.publicKey'))
            ."</div>";
        return $ret;
    }
}
