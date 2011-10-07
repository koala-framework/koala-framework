<?php
/**
 * @group Kwf_Component_Acl
 *
 * Special-Komponente (die erlaubt ist) liegt unter
 * - Root [Component]
 *   - SpecialContainer [Page] (erzeugt von CategoryGenerator; showInPageTreeAdmin=true)
 *     -> Special [Component]
 *   - PagesContainer [Page] (erzeugt von CategoryGenerator; showInPageTreeAdmin=true)
 *     - Pages [Component]
 *        - PageDetail [Page] (erzeugt von Generator_Static; showInPageTreeAdmin=false)
 *          -> Special [Component]
 */
class Kwf_Component_Acl_AllowedComponents_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Acl_AllowedComponents_Root');

        $acl = new Kwf_Acl();
        $this->_acl = $acl->getComponentAcl();
        $acl->addRole(new Zend_Acl_Role('special'));
        $this->_acl->allowComponent('special', 'Kwf_Component_Acl_AllowedComponents_Special');
    }

    public function testGetAllSpecial()
    {
        $cmps = Kwf_Component_Data_Root::getInstance()->getRecursiveChildComponents(array(
            'componentClass' => 'Kwf_Component_Acl_AllowedComponents_Special',
        ), array());
        $this->assertEquals(2, count($cmps));
    }

    public function testIt()
    {
        //darf nur eine sein, weil die zweite nicht im seitenbaum aufscheint
        $cmps = $this->_acl->getAllowedRecursiveChildComponents('special');
        $this->assertEquals(1, count($cmps));
    }
}
