<?= $this->doctype() ?>
<html>
    <head>
        <?= $this->debugData(); ?>
        <?= $this->assets('Admin') ?>
    </head>
    <body>

        <div class="x2-panel-bwrap" id="ext-gen12">
            <form id="ext-gen34" method="post" class="x2-form" action="<?=$this->action?>">
                <div class="x2-panel-mc" style="padding: 10px; height:100%">
                    <?php if ($this->text) { ?>
                        <div class="x2-form-item kwfLoginResult <?= $this->cssClass; ?>">
                            <span><?= Kwf_Util_HtmlSpecialChars::filter($this->text) ?></span>
                        </div>
                    <?php } else { ?>
                    <div tabindex="-1" class="x2-form-item">
                        <label class="x2-form-item-label" style="width: 75px;" for="ext-comp-1004"><?= trlKwf('E-Mail') ?>:</label>
                        <div style="padding-left: 80px;" id="x2-form-el-ext-comp-1004" class="x2-form-element">
                            <input type="text" name="username" autocomplete="on" size="20" class="x2-form-text x2-form-field" style="width: 180px;" />
                        </div>
                    </div>
                    <div tabindex="-1" class="x2-form-item">
                        <label class="x2-form-item-label" style="width: 75px;" for="ext-comp-1004"><?= trlKwf('Password') ?>:</label>
                        <div style="padding-left: 80px;" id="x2-form-el-ext-comp-1004" class="x2-form-element">
                            <input type="password" name="password" autocomplete="on" size="20" class="x2-form-text x2-form-field" style="width: 180px;" />
                        </div>
                    </div>
                    <div class="x2-form-clear-left"></div>
                    <table cellspacing="0" cellpadding="0" border="0" align="right" class="x2-btn-wrap x2-btn" id="ext-comp-1008" style="margin-top:10px; margin-right:16px;">
                        <tbody>
                            <tr>
                                <td style="width:20px;"></td>

                                <td class="x2-btn-left"></td>
                                <td class="x2-btn-center">
                                    <button type="submit" class="x2-btn-text" id="ext-gen18"><?= trlKwf('Login') ?></button>
                                </td>
                                <td class="x2-btn-right"></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } ?>
                    <div class="x2-clear"></div>
                </div>
            </form>
        </div>

    </body>
</html>
