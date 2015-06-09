<?php
class Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Root_Component');
    }

    /**
     This test creates the following Stucture:
     (root)
        - (table)
          - * [Foo]
            - (FooChild)
          - * (CreatesFooPage)
            - [Foo]
              - (FooChild)

    when searching for FooChild with getRecursiveChildComponents we must not get the one under CreatesFooPage
    */
    public function testIt()
    {
        $c = $this->_root->getRecursiveChildComponents(array(
            'componentClass' => 'Kwf_Component_Generator_GetRecursiveChildComponentsChildSelect_Table_Foo_FooChild_Component'
        ), array('page' => false));
        $this->assertEquals(0, count($c));
    }
}
