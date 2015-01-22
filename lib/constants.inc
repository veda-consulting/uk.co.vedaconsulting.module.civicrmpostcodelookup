<?php
################################################################

#

# QAS Pro On Demand - PHP Integration code

# (c) QAS Ltd - www.qas.com

#

#   constants.inc

# defines the action and parameter strings used in all scenarios

#

#   Defines the actions and parameters taken by control.php

#

################################################################





# Force these example pages to set charset UTF-8 in the HTTP header

#

# Integrators: You will probably have your own way to handle character

# set issues (if required). Be aware that the SOAP service returns

# UTF-8 encoded results. If you are intending to use country data that

# includes diacritic characters you must convert them if necessary to

# the character set in use on your pages (or elect to return the pages

# in UTF-8 as below)



//header ('Content-Type: text/html; charset=UTF-8');





# Configuration settings:

# the following values should be altered to suit your integration



# The URN of the OnDemand WSDL served by the OnDemand Server

# Because of a limitation in PHP with accessing WSDL documents over https, this should be downloaded

# from https://ws.ondemand.qas.com/ProOnDemand/V3/ProOnDemandService.asmx?WSDL and stored locally

// See llr_postcode_lookup.module llr_postcode_lookup_qas_parameters()
// define("CONTROL_WSDL_URN", dirname(__FILE__) . '/ProOnDemandService.wsdl');



#if connecting via a proxy, the following lines should be un-commented



#define("CONTROL_PROXY_NAME", "PROXY_SERVER");

#define("CONTROL_PROXY_PORT", 8080);

#define("CONTROL_PROXY_LOGIN", "PROXY_USER");

#define("CONTROL_PROXY_PASSWORD", "PROXY_PASSWORD");



# Username and password

# "External" username (Only LLR/LLR Customer Services use the "Internal" username - Via CiviCRM)
// See llr_postcode_lookup.module llr_postcode_lookup_qas_parameters()
// define("USERNAME", "e220d980-d4d");
// define("PASSWORD", "biscuitbase1");
