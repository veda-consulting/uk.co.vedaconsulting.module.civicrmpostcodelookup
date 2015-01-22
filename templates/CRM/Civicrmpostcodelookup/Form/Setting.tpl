{* HEADER *}

<!-- <div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div> -->

<div class="crm-block crm-form-block crm-export-form-block">

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

{* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

  <!-- <div>
    <span>{$form.favorite_color.label}</span>
    <span>{$form.favorite_color.html}</span>
  </div> -->

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

</div>

{literal}
<script>
cj( document ).ready(function() {
  cj('#server').parent().append('<br />Without trailing slash. Example: http://pce.afd.co.uk , http://civipostcode.com');
  hideAllFields();
  showFields();
  cj('#provider').change(function() {
    hideAllFields();
    showFields();
  });
});

function hideAllFields() {
  cj('#server').parent().parent().hide();
  cj('#api_key').parent().parent().hide();
  cj('#serial_number').parent().parent().hide();
  cj('#username').parent().parent().hide();
  cj('#password').parent().parent().hide();
}

function showFields() {

  var providerVal = cj("#provider").val();

  if (providerVal == 'experian') {
    cj('#username').parent().parent().show();
    cj('#password').parent().parent().show();
  }

  if (providerVal == 'afd') {
    cj('#server').parent().parent().show();
    cj('#serial_number').parent().parent().show();
    cj('#password').parent().parent().show();
    cj('#server').val('http://pce.afd.co.uk');
  }

  if (providerVal == 'civipostcode') {
    cj('#server').parent().parent().show();
    cj('#api_key').parent().parent().show();
    cj('#server').val('http://civipostcode.com');
  }

  if (providerVal == 'postcodeanywhere') {
    cj('#server').parent().parent().show();
    cj('#api_key').parent().parent().show();
    cj('#username').parent().parent().show();
    cj('#server').val('http://services.postcodeanywhere.co.uk');
  }
}
</script>
{/literal}


