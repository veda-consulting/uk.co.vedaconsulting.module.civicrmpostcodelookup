{literal}
<style type="text/css">
 .ui-autocomplete { height: 200px; overflow-y: scroll; overflow-x: hidden;}
</style>
<script type="text/javascript">
cj(document).ready(function(){
  var locationTypes = {/literal}{if $civiPostCodeLookupLocationTypeJson}{$civiPostCodeLookupLocationTypeJson}{else}''{/if}{literal};
  var blockId = '';
  var blockNo = '';  
  if (cj('#editrow-street_address-Primary').length > 0  ) {
       var blockId = 'Primary';
       var blockNo = 'Primary';
       var targetHtml = '';
       var postCodeHtml = '<div class="crm-section addressLookup form-item"><div class="label"><label for="addressLookup">Search for an address</label></div><div class="edit-value content"><div class="postcodelookup-textbox-wrapper"><input placeholder="Start typing a postcode" name="inputPostCode_' + blockId + '" id ="inputPostCode_' + blockId + '" style="width: 25em;"></div><div class="loader-image"><img id="loaderimage_' + blockId + '" src="{/literal}{$config->resourceBase}{literal}i/loading.gif" style="width:15px;height:15px; display: none" /></div></div><div class="clear"></div></div>';
       cj('#editrow-street_address-Primary').before(postCodeHtml);
    }

    else if (cj('#editrow-street_address-5').length > 0 ) {
       var blockId = '5';
       var blockNo = '5';
       var targetHtml = '';
       var divHtml = cj('#editrow-street_address-5').html();
       var postCodeHtml = '<div class="crm-section addressLookup form-item"><div class="label"><label for="addressLookup">Search for an address</label></div><div class="edit-value content"><div class="postcodelookup-textbox-wrapper"><input placeholder="Start typing a postcode" name="inputPostCode_' + blockId + '" id ="inputPostCode_' + blockId + '" style="width: 25em;"></div><div class="loader-image"><img id="loaderimage_' + blockId + '" src="{/literal}{$config->resourceBase}{literal}i/loading.gif" style="width:15px;height:15px; display: none" /></div></div><div class="clear"></div></div>';
       cj('#editrow-street_address-5').before(postCodeHtml);
    }

    // Include lookup in billing section as well
    if (cj('#billing_street_address-5').length > 0 ) {
       var billingblockId = '5';
       var billingblockNo = '5';
       var billingtargetHtml = '';
       var billingdivHtml = cj('#billing_street_address-5').html();
       var billingpostCodeHtml = '<div class="crm-section addressLookup form-item"><div class="label"><label for="addressLookup">Search for an address</label></div><div class="edit-value content"><div class="postcodelookup-textbox-wrapper"><input placeholder="Start typing a postcode" name="inputPostCodeBillingSection_' + billingblockId + '" id ="inputPostCodeBillingSection_' + billingblockId + '" style="width: 25em;"></div><div class="loader-image"><img id="loaderimage_' + billingblockId + '" src="{/literal}{$config->resourceBase}{literal}i/loading.gif" style="width:15px;height:15px; display: none" /></div></div><div class="clear"></div></div>';
       cj('.billing_street_address-5-section').before(billingpostCodeHtml);

       var billingPostcodeElement = '#inputPostCodeBillingSection_'+billingblockNo;
    }
    //Location Types from settings
    if (locationTypes) {
      cj.each(locationTypes, function (id, index) {
        if (cj('#editrow-street_address-'+ id).length > 0 ) {
         blockId = id;
         blockNo = id;
         var targetHtml = '';
         // var divHtml = cj('#editrow-street_address-'+ id).html();
         var postCodeHtml = '<div class="crm-section addressLookup form-item"><div class="label"><label for="addressLookup">Search for an address</label></div><div class="edit-value content"><div class="postcodelookup-textbox-wrapper"><input placeholder="Start typing a postcode" name="inputPostCode_' + blockId + '" id ="inputPostCode_' + blockId + '" style="width: 25em;"></div><div class="loader-image"><img id="loaderimage_' + blockId + '" src="{/literal}{$config->resourceBase}{literal}i/loading.gif" style="width:15px;height:15px; display: none" /></div></div><div class="clear"></div></div>';
         cj('#editrow-street_address-'+ id).before(postCodeHtml);
        }
      });
    }

  var buttonElement = '#postcodeLookupButton_'+blockNo;
  var houseElement = '#inputNumber_'+blockNo;
  var postcodeElement = '#inputPostCode_'+blockNo;
  var addressResultElement = '#addressResult_'+blockNo;
  var addressResultsElement = '#addressResults_'+blockNo;
  var minCharacters = 4;
  var delay = 200;

  var postcodeProvider = '{/literal}{$civiPostCodeLookupProvider}{literal}';
  if (postcodeProvider !== 'civipostcode') {
    cj(postcodeElement).attr("placeholder", "Type full postcode to find addresses");
    minCharacters = 5;
  }

  cj(function() {
    var sourceUrl = CRM.url('civicrm/{/literal}{$civiPostCodeLookupProvider}{literal}/ajax/search', {"json": 1});

    {/literal}{if $civiVersion < 4.5}{literal}

    cj( postcodeElement ).autocomplete( sourceUrl, {
        width: 400,
        selectFirst: false,
        minChars: minCharacters,
        matchContains: true,
        delay: delay,
        max: 1000,
        extraParams:{
          term:function () {
            return cj( postcodeElement ).val();
          },
          number:function () {
             return cj(houseElement).val();
          }
        }
    }).result(function(event, data, formatted) {
       findAddressValues(data[1], blockNo, blockPrefix = '');
       cj(postcodeElement).val('');
       return false;
    });

    // Postcode lookup in billing section
    if (cj('#billing_street_address-5').length > 0 ) {
      cj( billingPostcodeElement ).autocomplete( sourceUrl, {
          width: 400,
          selectFirst: false,
          minChars: minCharacters,
          matchContains: true,
          delay: delay,
          max: 1000,
          extraParams:{
            term:function () {
              return cj( billingPostcodeElement ).val();
            },
            number:function () {
               return cj(houseElement).val();
            }
          }
      }).result(function(event, data, formatted) {
         findAddressValues(data[1], '5', blockPrefix = 'billing_');
         cj(billingPostcodeElement).val('');
         return false;
      });
    }

    {/literal}{else}{literal}

    cj(postcodeElement).autocomplete({
      source: sourceUrl,
      minLength: minCharacters,
      data: {postcode: cj( postcodeElement ).val(), number: cj(houseElement).val(), mode: '0'},
      search: function( event, ui ) {
        cj('#loaderimage_'+blockNo).show();
      },
      response: function( event, ui ) {
        cj('#loaderimage_'+blockNo).hide();
      },
      select: function(event, ui) {
        if (ui.item.id != '') {
          findAddressValues(ui.item.id, blockNo, blockPrefix = '');
          cj('#loaderimage_'+blockNo).show();
        }
        return false;
      },

      html: true, // optional (jquery.ui.autocomplete.html.js required)

      //optional (if other layers overlap autocomplete list)
      open: function(event, ui) {
        cj(".ui-autocomplete").css("z-index", 1000);
      }
    });

    // Postcode lookup in billing section
    if (cj('#billing_street_address-5').length > 0 ) {
      cj(billingPostcodeElement).autocomplete({
        source: sourceUrl,
        minLength: minCharacters,
        data: {postcode: cj( billingPostcodeElement ).val(), number: cj(houseElement).val(), mode: '0'},
        search: function( event, ui ) {
          cj('#loaderimage_'+blockNo).show();
        },
        response: function( event, ui ) {
          cj('#loaderimage_'+blockNo).hide();
        },
        select: function(event, ui) {
          if (ui.item.id != '') {
            findAddressValues(ui.item.id, '5', blockPrefix = 'billing_');
            cj('#loaderimage_'+blockNo).show();
          }
          return false;
        },

        html: true, // optional (jquery.ui.autocomplete.html.js required)

        //optional (if other layers overlap autocomplete list)
        open: function(event, ui) {
          cj(".ui-autocomplete").css("z-index", 1000);
        }
      });
    }

    {/literal}{/if}{literal}

  });
});

function findAddressValues(id , blockNo, blockPrefix) {
  cj('#loaderimage_'+blockNo).show();
  setAddressFields(false, blockNo, blockPrefix);
  var sourceUrl = CRM.url('civicrm/{/literal}{$civiPostCodeLookupProvider}{literal}/ajax/get', {"json": 1});
  cj.ajax({
    dataType: 'json',
    data: {id: id, mode: '0'},
    url: sourceUrl,
    success: function (data) {
      setAddressFields(data.address, blockNo, blockPrefix);
      setAddressFields(true, blockNo, blockPrefix);
      cj('#loaderimage_'+blockNo).hide();
    }
  });
}

function setAddressFields(address, blockNo, blockPrefix) {
   var postcodeElement = '#' + blockPrefix + 'postal_code-'+ blockNo;
   var streetAddressElement = '#' + blockPrefix + 'street_address-'+ blockNo;
   var AddstreetAddressElement = '#' + blockPrefix + 'supplemental_address_1-'+ blockNo;
   var AddstreetAddressElement1 = '#' + blockPrefix + 'supplemental_address_2-'+ blockNo;
   var cityElement = '#' + blockPrefix + 'city-'+ blockNo;
   var countyElement = '#address_'+ blockNo +'_state_province_id';

   var allFields = {
    postcode: postcodeElement,
    line1: streetAddressElement,
    line2: AddstreetAddressElement,
    line3: AddstreetAddressElement1,
    city: cityElement
   };

  if(address == true) {
    for(var field in allFields) {
      cj(allFields[field]).removeAttr('disabled');
    }
  }
   else if(address == false) {
    for(var field in allFields) {
      cj(allFields[field]).attr('disabled', 'disabled');
    }
  }
  else {
    cj(streetAddressElement).val('');
    cj(AddstreetAddressElement).val('');
    cj(AddstreetAddressElement1).val('');
    cj(cityElement).val('');
    cj(postcodeElement).val('');
    cj(countyElement).val('');

    cj(streetAddressElement).val(address.street_address);
    cj(AddstreetAddressElement).val(address.supplemental_address_1);
    cj(AddstreetAddressElement1).val(address.supplemental_address_2);
    cj(cityElement).val(address.town);
    cj(postcodeElement).val(address.postcode);
    if(typeof(address.state_province_id) != "undefined" && address.state_province_id !== null) {
       cj(countyElement).val(address.state_province_id);
    }
    cj(countyElement).trigger("change");
  }
}
</script>
{/literal}
