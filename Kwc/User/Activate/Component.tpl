<div class="<?=$this->cssClass?>">
    <h1><?=$this->data->trlKwf('Activate Useraccount')?></h1>
    <? if ($this->showPassword) { ?>
    <div class="form">
        <h2><?=trlKwf('Set Password:')?></h2>
        <p>
            <?=$this->data->trlKwf('Plese enter in both fields the password which you want to use for your useraccount')?>.<br />
            <?=$this->data->trlKwf('After the activation you are automatically logged in and you could use your account.')?>
        </p>
        <?=$this->component($this->form)?>
    </div>
    <? } ?>
    <? if ($this->redirects) { ?>
        <h2><?=trlKwf('Activate with:')?></h2>
        <ul class="redirects">
        <?php foreach ($this->redirects as $r) { ?>
            <li>
                <form method="GET" action="<?=htmlspecialchars($r['url'])?>">
                <input type="hidden" name="redirectAuth" value="<?=htmlspecialchars($r['authMethod'])?>" />
                <input type="hidden" name="code" value="<?=htmlspecialchars($r['code'])?>" />
                <?=$r['formOptions']?>
                <button>
                    <? if ($r['icon']) { ?>
                        <img src="<?=htmlspecialchars($r['icon'])?>" />
                    <? } else { ?>
                        <?=htmlspecialchars($r['name'])?>
                    <? } ?>
                </button>
                </form>
            </li>
        <?php } ?>
        </ul>
    <? } ?>
</div>
