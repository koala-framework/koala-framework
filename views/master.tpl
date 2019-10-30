<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title><?php echo $this->applicationName; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noindex, nofollow" />
    <?php if ($this->favicon) { ?>
    <link rel="shortcut icon" href="<?php echo Kwf_Util_HtmlSpecialChars::filter($this->favicon)?>" />
    <?php } ?>
    <?php echo $this->partial($this->extTemplate, $this) ?>
  </head>
  <body>
  </body>
</html>
