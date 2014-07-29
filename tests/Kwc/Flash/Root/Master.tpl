<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head><?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwc_Flash', 'TestFiles', 'Kwc_Flash_Root_Component'))?></head>
    <body><?=$this->componentWithMaster($this->componentWithMaster)?></body>
</html>
