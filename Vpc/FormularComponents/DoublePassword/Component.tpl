<?php $this->component($this->password1) ?>
<div class="vpcDoublePasswordField">
    <label><?php if ($this->password2['store']['isMandatory']) echo ' * ' ?><?= $this->password2['store']['fieldLabel'] ?></label>
    <div class="field"><?php $this->component($this->password2) ?></div>
</div>
