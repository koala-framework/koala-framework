<?= $this->doctype() ?>
<html>
    <head>
        <?= $this->debugData(); ?>
        <?= $this->assets('Admin') ?>
    </head>
    <body>

        <div class="x-panel-bwrap" id="ext-gen12">
            <form id="ext-gen34" method="post" class="x-form" action="/vps/user/login/show-form">
                <div class="x-panel-mc" style="padding: 10px; height:100%">
                    <div tabindex="-1" class="x-form-item">
                        <label class="x-form-item-label" style="width: 75px;" for="ext-comp-1004"><?= trlVps('E-Mail') ?>:</label>
                        <div style="padding-left: 80px;" id="x-form-el-ext-comp-1004" class="x-form-element">
                            <input type="text" name="username" autocomplete="on" size="20" class="x-form-text x-form-field" style="width: 180px;" />
                        </div>
                    </div>
                    <div tabindex="-1" class="x-form-item">
                        <label class="x-form-item-label" style="width: 75px;" for="ext-comp-1004"><?= trlVps('Password') ?>:</label>
                        <div style="padding-left: 80px;" id="x-form-el-ext-comp-1004" class="x-form-element">
                            <input type="password" name="password" autocomplete="on" size="20" class="x-form-text x-form-field" style="width: 180px;" />
                        </div>
                    </div>
                    <? if ($this->text) { ?>
                        <div class="x-form-item vpsLoginResult <?= $this->cssClass; ?>">
                            <span><?= $this->text ?></span>
                        </div>
                    <? } else { ?>
                        <div tabindex="-1" class="x-form-item vpsLoginResult"><span>&nbsp;</span></div>
                    <? } ?>
                    <div class="x-form-clear-left"></div>
                    <table cellspacing="0" cellpadding="0" border="0" align="right" class="x-btn-wrap x-btn" id="ext-comp-1008" style="margin-top:12px; margin-right:16px;">
                        <tbody>
                            <tr>
                                <td style="width:20px;"></td>

                                <td class="x-btn-left"></td>
                                <td class="x-btn-center">
                                    <button type="submit" class="x-btn-text" id="ext-gen18"><?= trlVps('Login') ?></button>
                                </td>
                                <td class="x-btn-right"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="x-clear"></div>
                </div>
            </form>
        </div>

    </body>
</html>
