{literal}
<script type="text/javascript">
cj(document).ready(function(){
  if (cj('#editrow-street_address-Primary').length > 0  ) {
       var blockId = 'Primary';
       var blockNo = 'Primary';
       var targetHtml = '';
       var postCodeHtml = '<div class="crm-section addressLookup form-item"><div class="label"><label for="addressLookup">Search for an address</label></div><div class="edit-value content"><div class="postcodelookup-textbox-wrapper"><input placeholder="Start typing a postcode" name="inputPostCode_' + blockId + '" id ="inputPostCode_' + blockId + '"></div><div class="loader-image"><img id="loaderimage_' + blockId + '" src="{/literal}{$config->resourceBase}{literal}i/loading.gif" style="width:15px;height:15px; display: none" /></div></div><div class="clear"></div></div>';
       cj('#editrow-street_address-Primary').before(postCodeHtml);
    }

    else if (cj('#editrow-street_address-5').length > 0 ) {
       var blockId = '5';
       var blockNo = '5';
       var targetHtml = '';
       var divHtml = cj('#editrow-street_address-5').html();
       var postCodeHtml = '<div class="crm-section addressLookup form-item"><div class="label"><label for="addressLookup">Search for an address</label></div><div class="edit-value content"><div class="postcodelookup-textbox-wrapper"><input placeholder="Start typing a postcode" name="inputPostCode_' + blockId + '" id ="inputPostCode_' + blockId + '"></div><div class="loader-image"><img id="loaderimage_' + blockId + '" src="{/literal}{$config->resourceBase}{literal}i/loading.gif" style="width:15px;height:15px; display: none" /></div></div><div class="clear"></div></div>';
       cj('#editrow-street_address-5').before(postCodeHtml);
    }

  var buttonElement = '#postcodeLookupButton_'+blockNo;
  var houseElement = '#inputNumber_'+blockNo;
  var postcodeElement = '#inputPostCode_'+blockNo;
  var addressResultElement = '#addressResult_'+blockNo;
  var addressResultsElement = '#addressResults_'+blockNo;
  var minCharacters = 4;

  var postcodeProvider = '{/literal}{$config->CiviPostCodeLookupProvider}{literal}';
  if (postcodeProvider !== 'civipostcode') {
    cj(postcodeElement).attr("placeholder", "Type full postcode to find addresses");
    minCharacters = 5;
  }

  cj(function() {
    cj(postcodeElement).autocomplete({
      source: '/civicrm/{/literal}{$config->CiviPostCodeLookupProvider}{literal}/ajax/search',
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
          findAddressValues(ui.item.id, blockNo);
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
  });
});

function findAddressValues(id , blockNo) {
  setAddressFields(false, blockNo);
  cj.ajax({
    dataType: 'json',
    data: {id: id, mode: '0'},
    url: '/civicrm/{/literal}{$config->CiviPostCodeLookupProvider}{literal}/ajax/get',
    success: function (data) {
      setAddressFields(data.address, blockNo);
      setAddressFields(true, blockNo);
      cj('#loaderimage_'+blockNo).hide();
    }
  });
}

function setAddressFields(address, blockNo) {
   var postcodeElement = '#postal_code-'+ blockNo;
   var streetAddressElement = '#street_address-'+ blockNo;
   var AddstreetAddressElement = '#supplemental_address_1-'+ blockNo;
   var AddstreetAddressElement1 = '#supplemental_address_2-'+ blockNo;
   var cityElement = '#city-'+ blockNo;

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

    cj(streetAddressElement).val(address.street);
    cj(AddstreetAddressElement).val(address.locality);
    cj(cityElement).val(address.town);
    cj(postcodeElement).val(address.postcode);
  }
}
</script>
{/literal}
