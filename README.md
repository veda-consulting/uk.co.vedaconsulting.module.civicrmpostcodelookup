# Postcode lookup for CiviCRM #

### Overview ###

For having postcode lookup feature in CiviCRM backend and Front end profiles.

### Supported Providers ###

* [AFD](http://www.afd.co.uk)
* [Civipostcode](http://civipostcode.com/)
* [Experian](http://www.qas.co.uk)
* [PostcodeAnywhere](http://www.postcodeanywhere.co.uk/)

### Installation ###

* Install the extension manually in CiviCRM. More details [here](http://wiki.civicrm.org/confluence/display/CRMDOC/Extensions#Extensions-Installinganewextension) about installing extensions in CiviCRM.
* Configure postcode lookup provider details in Administer >> Postcode Lookup(civicrm/postcodelookup/settings?reset=1)

#### Integration with Drupal Webform
This drupal module provides integration with Drupal Webform: https://github.com/compucorp/webform_civicrm_postcode

### Usage ###

* For backend, postcode lookup features is automatically enabled for address fields when adding/editing contacts and configuring event location.
* For front end profiles, postcode lookup feature is enabled only if 'Street Address' field of type 'Primary' or 'Billing' is added to the profile. Include 'Supplemental Address 1' and 'Supplemental Address 2' fields in the profile for address lines based on the rules in the Royal Mail programmers guide.

### Support ###

support (at) vedaconsulting.co.uk
