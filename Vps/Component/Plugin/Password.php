<?php
/**
 * @deprecated use Vps_Component_Plugin_Password_Component instead
 */
class Vps_Component_Plugin_Password extends Vps_Component_Plugin_View_Abstract
{
    protected $_password = 'planet';

    public function processOutput($output)
    {
        if (!is_array($this->_password)) $this->_password = array($this->_password);

        $msg = '';
        $session = new Zend_Session_Namespace('password');
        if (isset($_POST['password'])) {
            if (in_array($_POST['password'], $this->_password)) {
                $session->login = true;
            } else {
                $msg = trlVps('Invalid Password');
            }
        }
        if ($session->login) return $output;

        return '<form action="" method="post">
        '.$msg.'
        <input type="password" name="password" />
        <input type="submit" value="login" />
        </form>';
    }
}
