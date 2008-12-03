<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/assets/vps/css/placeholder.css" />
  </head>
  <body>
    <div class="placeholder">
        <p class="first">Hier entsteht für unseren Kunden<br />
        <strong><?=str_replace('www.', '', $_SERVER['HTTP_HOST'])?></strong><br />
        eine Internetpräsentation.</p>
        <p>Wir bitten Sie, uns in naher Zukunft wieder zu besuchen.</p>
    </div>
    <p class="copy">© 2000 - <?=date('Y')?> Vivid Planet Software GmbH</p>
  </body>
</html>
