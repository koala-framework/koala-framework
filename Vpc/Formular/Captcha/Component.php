<?php
class Vpc_Formular_Captcha_Component extends Vpc_Formular_Field_Abstract
{
    private $path = '';
    var $width       = 142;
    var $height      = 40;
    var $jpg_quality = 30;

    const NAME = 'Formular.Captcha';

    public function getTemplateVars()
    {
        $value = $this->_generateValue();
        $encrypt = $this->encrypt($value);
        $path = '/admin/component/edit/Vpc_Formular_Captcha_Show/' . $this->getId() . '/';
        $path .= '?showPic=' . $encrypt;

        $return = parent::getTemplateVars();
        $return['encrypt'] = $encrypt;
        $return['value'] = $value;
        $return['path'] = $path;
        $return['id'] = $this->getId();
        $return['captcha'] = 'CaptchaTest';
        $return['template'] = 'Formular/Captcha.html';
        return $return;
    }

    /**
     * Generiert 5 Zufallswerte
     */
    private function _generateValue()
    {
        $num_chars = 5;
        // define characters of which the captcha can consist
        $alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
        'N','P','Q','R','S','T','U','V','W','X','Y','Z',
        '1','2','3','4','5','6','7','8','9' );

        $max = count($alphabet);

        // generate random string
        $captcha_str = '';
        for ($i=1; $i<=$num_chars; $i++) {
            // choose randomly a character from alphabet and append it to string
            $chosen = rand(1, $max);
            $captcha_str .= $alphabet[$chosen - 1];
        }
        return $captcha_str;
    }

    /**
     * generiert ein Bild
     */
    public function generateImage($char_seq )
    {
        $num_chars = strlen($char_seq);
        $img = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($img, 1);
        imagecolortransparent($img);

        // generate background of randomly built ellipses
        for ($i=1; $i<=200; $i++) {
            $r = round(rand(0, 100));
            $g = round(rand(0, 100));
            $b = round(rand(0, 110));
            $color = imagecolorallocate($img, $r, $g, $b);
            imagefilledellipse($img, round(rand(0, $this->width)),
                                     round(rand(0, $this->height)),
                                     round(rand(0, $this->width/16)),
                                     round(rand(0, $this->height/4)), $color);
        }

        $start_x = round($this->width / $num_chars);
        $max_font_size = $start_x;
        $start_x = round(0.5 * $start_x);
        $max_x_ofs = round($max_font_size*0.9);

        // set each letter with random angle, size and color
        for ($i=0; $i<=$num_chars; $i++) {
            $r = round(rand(127, 255));
            $g = round(rand(127, 255));
            $b = round(rand(127, 255));
            $y_pos = ($this->height/2)+round(rand(5, 20));

            $fontsize = round(rand(15, $max_font_size));
            $color = imagecolorallocate($img, $r, $g, $b);
            $presign = round(rand(0, 1));
            $angle = round(rand(0, 25));
            if ($presign==true) {
                $angle = -1*$angle;
            }
            //ImageString($img, $fontsize, $start_x+$i*$max_x_ofs, 10, substr($char_seq,$i,1), $color);
            ImageTTFText($img, $fontsize, $angle, $start_x + $i * $max_x_ofs, $y_pos, $color, dirname(__FILE__).'/verdana.ttf', substr($char_seq, $i, 1));

        }
        return $img;
    }


  /**
   * Algorithmus zum Verschlüsseln
   */
    public function encrypt($value)
    {
        $value = str_replace('2', '301', $value);
        $value = str_replace('1', '302', $value);
        $value = str_replace('3', '304', $value);
        $value = str_replace('4', '306', $value);
        $value = str_replace('5', '307', $value);
        $value = str_replace('6', '308', $value);
        $value = str_replace('7', '309', $value);
        $value = str_replace('8', '310', $value);
        $value = str_replace('9', '311', $value);

        $value = str_replace('O', '312', $value);
        $value = str_replace('X', '314', $value);
        $value = str_replace('Y', '315', $value);
        $value = str_replace('Z', '316', $value);
        $value = str_replace('G', '317', $value);

        $value = str_replace('P', '318', $value);
        $value = str_replace('Q', '319', $value);
        $value = str_replace('R', '320', $value);
        $value = str_replace('S', '322', $value);
        $value = str_replace('T', '324', $value);
        $value = str_replace('U', '325', $value);
        $value = str_replace('V', '326', $value);
        $value = str_replace('W', '327', $value);

        $value =str_replace('H', '328', $value);
        $value = str_replace('I', '329', $value);
        $value = str_replace('J', '340', $value);
        $value = str_replace('K', '341', $value);
        $value = str_replace('L', '342', $value);
        $value = str_replace('K', '344', $value);
        $value = str_replace('N', '345', $value);

        $value = str_replace('A', '346', $value);
        $value = str_replace('B', '347', $value);
        $value = str_replace('C', '348', $value);
        $value = str_replace('D', '349', $value);
        $value = str_replace('E', '350', $value);
        $value = str_replace('F', '351', $value);

        $value = str_replace('1', 'a', $value);
        $value = str_replace('5', 'b', $value);
        $value = str_replace('4', 'c', $value);
        $value = str_replace('8', 'd', $value);
        $value = str_replace('2', 'e', $value);
        $value = str_replace('3', 'f', $value);
        $value = str_replace('9', 'g', $value);
        $value = str_replace('6', 'h', $value);
        $value = str_replace('7', 'i', $value);

        $value = strrev($value);

        $check = '';
        $cnt = 0;
        $finalString = "";
        for ($i=0; $i < strlen($value); $i++) {
            $temp = $value[$i];
            if ($temp != $check) {
                if ($cnt != 0) {
                    $finalString .= $cnt;
                }
                $cnt = 1;
                $finalString .= $temp;
                $check = $temp;

                if ($i == strlen($value)-1) {
                    $finalString .= $cnt;
                }
            } else {
                $cnt++;
                if ($i == strlen($value)-1) {
                    $finalString .= $cnt;
                }
            }
        }
        return $finalString;
    }

    public function decrypt($newString)
    {
        $value = "";
        $check = '';
        $cnt = 0;
        $finalString = "";
        for ($i=0; $i<strlen($newString); $i++) {
            $temp = $newString[$i];
            if ($temp == '1' || $temp == '2' || $temp == '3' || $temp == '4' ||
            $temp == '5' || $temp == '6' || $temp == '7' || $temp == '8' || $temp == '9') {

                for ($j = 0; $j < $temp; $j++) {
                    $value .= $check;
                }
            } else {
                $check = $temp;
            }
        }

        $value = str_replace('a', '1', $value);
        $value = str_replace('b', '5', $value);
        $value = str_replace('c', '4', $value);
        $value = str_replace('d', '8', $value);
        $value = str_replace('e', '2', $value);
        $value = str_replace('f', '3', $value);
        $value = str_replace('g', '9', $value);
        $value = str_replace('h', '6', $value);
        $value = str_replace('i', '7', $value);

        $value = strrev($value);

        $value = str_replace('312', 'O', $value);
        $value = str_replace('314', 'X', $value);
        $value = str_replace('315', 'Y', $value);
        $value = str_replace('316', 'Z', $value);
        $value = str_replace('317', 'G', $value);

        $value = str_replace('318', 'P', $value);
        $value = str_replace('319', 'Q', $value);
        $value = str_replace('320', 'R', $value);
        $value = str_replace('322', 'S', $value);
        $value = str_replace('324', 'T', $value);
        $value = str_replace('325', 'U', $value);
        $value = str_replace('326', 'V', $value);
        $value = str_replace('327', 'W', $value);

        $value =str_replace('328', 'H', $value);
        $value = str_replace('329', 'I', $value);
        $value = str_replace('340', 'J', $value);
        $value = str_replace('341', 'K', $value);
        $value = str_replace('342', 'L', $value);
        $value = str_replace('344', 'M', $value);
        $value = str_replace('345', 'N', $value);

        $value = str_replace('346', 'A', $value);
        $value = str_replace('347', 'B', $value);
        $value = str_replace('348', 'C', $value);
        $value = str_replace('349', 'D', $value);
        $value = str_replace('350', 'E', $value);
        $value = str_replace('351', 'F', $value);

        $value = str_replace('311', '9', $value);
        $value = str_replace('310', '8', $value);
        $value = str_replace('309', '7', $value);
        $value = str_replace('308', '6', $value);
        $value = str_replace('307', '5', $value);
        $value = str_replace('306', '4', $value);
        $value = str_replace('304', '3', $value);
        $value = str_replace('302', '1', $value);
        $value = str_replace('301', '2', $value);

        return $value;

    }

    public function validateField($mandatory){
        if (isset($_POST['hidden']) && isset($_POST['captcha'])) {
            $code = $_POST['hidden'];
            $captcha = $_POST['captcha'];
            if ($code != $this->encrypt($captcha)) {
                return 'Das Captcha Feld muss korrekt ausgefüllt werden';
            }
        }
        return '';
    }
}
