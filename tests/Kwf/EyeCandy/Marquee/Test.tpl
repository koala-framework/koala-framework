<?= $this->doctype('XHTML1_STRICT') ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?=$this->assets('Vps_EyeCandy_Marquee:Test')?>
    </head>
    <body>
        <div class="vpsMarqueeElements">
            <input type="hidden" class="settings" value="<?=str_replace("\"", "'",Zend_Json::encode($this->settings))?>" />
            <div style="background-color:red">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis ipsum. Nunc sed justo.</div>
            <div style="background-color:blue">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis ipsum. Nunc sed justo. Phasellus ultricies dui a justo. Vivamus ac lacus id ipsum auctor condimentum. Nam imperdiet lacus quis risus. Morbi viverra, sem sed dictum vulputate, risus diam blandit est, at sodales urna ipsum elementum ante. Vivamus sem. Aliquam erat volutpat. Nunc scelerisque ante ac erat semper malesuada. Proin tellus erat, tristique eu, convallis sit amet, auctor id, nunc. Donec sit amet sapien. Fusce sed est quis ligula consequat luctus. Sed sodales.</div>
            <div style="background-color:yellow">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis ipsum. Nunc sed justo. Phasellus ultricies dui a justo.</div>
        </div>
    </body>
</html>
