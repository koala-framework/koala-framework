<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title><?= $this->applicationName; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <? if ($this->favicon) { ?>
    <link rel="shortcut icon" href="<?=htmlspecialchars($this->favicon)?>" />
    <? } ?>
    <?= $this->partial($this->extTemplate, $this) ?>
  </head>
  <body>
  </body>
</html>
