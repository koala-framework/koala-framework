<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?=$this->assets(new Kwf_Assets_Package_TestPackage('Kwf_Media_Headline'))?>
  </head>
  <body>
    <h1 class="testHeadline">Foo</h1>
    <h1>Bar</h1>
  </body>
</html>
