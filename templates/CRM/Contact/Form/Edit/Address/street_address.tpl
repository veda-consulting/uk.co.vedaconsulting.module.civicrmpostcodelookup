{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2014                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}

<tr id="addressLookup">
  <td colspan="2">
    <label for="addressLookup">Search for an address</label><br>
    <input placeholder="Start typing a postcode" name="inputPostCode_{$blockId}" id ="inputPostCode_{$blockId}">
    &nbsp;&nbsp;<img id="loaderimage_{$blockId}" src="{$config->resourceBase}i/loading.gif" style="width:15px;height:15px; display: none" />
  </td>
</tr>

{literal}
<style type="text/css">
 .ui-autocomplete { height: 200px; overflow-y: scroll; overflow-x: hidden;}
</style>
<script type="text/javascript">
var info = []; //Needed to access data from outside the JSON processing function
cj(document).ready(function() {
  var blockNo = {/literal}{$blockId}{literal};
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
            return  cj( postcodeElement ).val();
          },
          number:function () {
             return cj(houseElement).val();
          }
        }
    }).result(function(event, data, formatted) {
       findAddressValues(data[1], blockNo);
       cj(postcodeElement).val('');
       return false;
    });

    {/literal}{else}{literal}

    cj(postcodeElement).autocomplete({
        source: sourceUrl,
        minLength: minCharacters,
        delay: delay,
        data: {postcode: cj( postcodeElement ).val(), number: cj(houseElement).val(), mode: '0'},
        //max: {/literal}{crmSetting name="search_autocomplete_count" group="Search Preferences"}{literal},
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

    {/literal}{/if}{literal}

  });
});

function addslashes (str) {
    return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
}

function findAddressValues(id , blockNo) {
  cj('#loaderimage_'+blockNo).show();
  setAddressFields(false, blockNo);
  var sourceUrl = CRM.url('civicrm/{/literal}{$civiPostCodeLookupProvider}{literal}/ajax/get', {"json": 1});
  cj.ajax({
    dataType: 'json',
    data: {id: id},
    url: sourceUrl,
    success: function (data) {
      setAddressFields(data.address, blockNo);
      setAddressFields(true, blockNo);
      cj('#loaderimage_'+blockNo).hide();
    }
  });
}

function setAddressFields(address, blockNo) {
   var postcodeElement = '#address_'+ blockNo +'_postal_code';
   var streetAddressElement = '#address_'+ blockNo +'_street_address';
   var AddstreetAddressElement = '#address_'+ blockNo +'_supplemental_address_1';
   var AddstreetAddressElement1 = '#address_'+ blockNo +'_supplemental_address_2';
   var cityElement = '#address_'+ blockNo +'_city';
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

{if !empty($form.address.$blockId.street_address)}
   <tr id="streetAddress_{$blockId}">
    <td colspan="2">
      {$form.address.$blockId.street_address.label} {help id="id-street-address" file="CRM/Contact/Form/Contact.hlp"}<br />
      {$form.address.$blockId.street_address.html}
      {if $parseStreetAddress eq 1 && ($action eq 1 || $action eq 2)}
          &nbsp;&nbsp;<a href="#" title="{ts}Edit Address Elements{/ts}" onClick="processAddressFields( 'addressElements' , '{$blockId}', 1 );return false;">{ts}Edit Address Elements{/ts}</a>
          {help id="id-edit-street-elements" file="CRM/Contact/Form/Contact.hlp"}
      {/if}
    </td>
  </tr>

  {if $parseStreetAddress eq 1 && ($action eq 1 || $action eq 2)}
    <tr id="addressElements_{$blockId}" class=hiddenElement>
      <td>
         {$form.address.$blockId.street_number.label}<br />
         {$form.address.$blockId.street_number.html}
       </td>

      <td>
         {$form.address.$blockId.street_name.label}<br />
         {$form.address.$blockId.street_name.html}<br />
      </td>

      <td colspan="2">
        {$form.address.$blockId.street_unit.label}<br />
        {$form.address.$blockId.street_unit.html}
        <a href="#" title="{ts}Edit Street Address{/ts}" onClick="processAddressFields( 'streetAddress', '{$blockId}', 1 );return false;">{ts}Edit Complete Street Address{/ts}</a>
        {help id="id-edit-complete-street" file="CRM/Contact/Form/Contact.hlp"}
      </td>
    </tr>
  {/if}

{if $parseStreetAddress eq 1}
{literal}
<script type="text/javascript">
function processAddressFields( name, blockId, loadData ) {

  if ( loadData ) {
            var allAddressValues = {/literal}{if $allAddressFieldValues}{$allAddressFieldValues}{else}''{/if}{literal};

      var streetName    = eval( "allAddressValues.street_name_"    + blockId );
      if (streetName === null) streetName = '';
      var streetUnit    = eval( "allAddressValues.street_unit_"    + blockId );
      if (streetUnit === null) streetUnit = '';
      var streetNumber  = eval( "allAddressValues.street_number_"  + blockId );
      if (streetNumber === null) streetNumber = '';
      var streetAddress = eval( "allAddressValues.street_address_" + blockId );
      if (streetAddress === null) streetAddress = '';
  }

        if ( name == 'addressElements' ) {
             if ( loadData ) {
            streetAddress = '';
       }

       cj('#addressElements_' + blockId).show();
       cj('#streetAddress_' + blockId).hide();
  } else {
             if ( loadData ) {
                  streetNumber = streetName = streetUnit = '';
             }

             cj('#streetAddress_' +  blockId).show();
             cj('#addressElements_'+ blockId).hide();
       }

       // set the values.
       if ( loadData ) {
          cj( '#address_' + blockId +'_street_name'    ).val( streetName    );
          cj( '#address_' + blockId +'_street_unit'    ).val( streetUnit    );
          cj( '#address_' + blockId +'_street_number'  ).val( streetNumber  );
          cj( '#address_' + blockId +'_street_address' ).val( streetAddress );
       }
}

</script>
{/literal}
{/if}
{/if}

