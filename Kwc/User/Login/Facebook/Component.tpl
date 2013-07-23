<div class="<?=$this->cssClass?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <a href="#" onclick="return false;" class="kwfFbLoginButton"><?=$this->data->trl('login using facebook')?></a>
    <div class="success">
        <?=$this->component($this->success);?>
    </div>
</div>