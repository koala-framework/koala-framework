{component component=$component.password1}
<label>{if $component.password2.store.isMandatory} * {/if} {$component.password2.store.fieldLabel}</label>
<div class="field">{component component=$component.password2}</div>
