<?php
/**
 * @deprecated use Kwf_Component_Plugin_Password_Component instead
 */
class Kwf_Component_Plugin_Password extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace
{
    protected $_password = 'planet';

    public function replaceOutput($output, $renderer)
    {
        if (!is_array($this->_password)) $this->_password = array($this->_password);

        $msg = '';
        $session = new Kwf_Session_Namespace('password');
        if (isset($_POST['password'])) {
            if (in_array($_POST['password'], $this->_password)) {
                $session->login = true;
            } else {
                $msg = trlKwf('Invalid Password');
            }
        }
        if ($session->login) return false;

        return '<form action="" method="post">
        '.$msg.'
        <input type="password" name="password" />
        <input type="submit" value="login" />
        </form>';
    }
}
