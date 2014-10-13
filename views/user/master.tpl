<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title><?php echo $this->applicationName; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php if ($this->favicon) { ?>
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($this->favicon)?>" />
    <?php } ?>
    <?=$this->assets($this->dep)?>
  </head>
  <body class="backendUser">
    <?php echo $this->render($this->contentScript) ?>
  </body>
</html>
