<?php
REQUIRE_ONCE("CommonClasses.php");

REQUIRE_ONCE("IQASCapture.php");



/**

 * 

 * Quick Address

 * @author aikkeongt

 *

 */

class QASCapture implements IQASCapture

{

	private $soap = NULL;

	

	const PARAM_COUNTRY = "Country";

	const PARAM_ENGINE = "Engine";

	const PARAM_PROMPTSET = "PromptSet";

	const PARAM_REQUEST_TAG = "RequestTag";

	const PARAM_THRESHOLD = "Threshold";

	const PARAM_TIMEOUT = "Timeout";

	const PARAM_LAYOUT = "Layout";

	const PARAM_SEARCH = "Search";

	const PARAM_MONIKER = "Moniker";

	const PARAM_FLATTEN = "Flatten";

	const PARAM_REFINEMENT = "Refinement"; 

	const SOAP_HEADER_NAMESPACE = "http://www.qas.com/OnDemand-2011-03";

  // Replaced CONTROL_WSDL_URN, USERNAME and PASSWORD constants with values
  // passed in to the constructor
  private $wsdl_file_url = '';
  private $username = '';
  private $password = '';

	/**

	 * constructor

	 * @param array $service_params
	 *   Array containing various configuration paramteres enabling use of the
	 *   QAS web service(s)

	 */

	// public function QASCapture($endPointURL)
  public function QASCapture($service_params)
  {
    if (isset($service_params['wsdl_file_url'])) {
      $this->wsdl_file_url = $service_params['wsdl_file_url'];
    }
    else {
      $this->wsdl_file_url = dirname(__FILE__) . '/ProOnDemandService.wsdl';
    }

    if (isset($service_params['username'])) {
      $this->username = $service_params['username'];
    }

    if (isset($service_params['password'])) {
      $this->password = $service_params['password'];
    }

    if(defined('CONTROL_PROXY_NAME'))

		{

			$this->soap = new SoapClient($this->wsdl_file_url, 

										 array('soap_version' => SOAP_1_2,

												 'exceptions' => 0,

												 'classmap' => array('QAAuthentication' => 'QAAuthentication',

												 				     'QAQueryHeader' => 'QAQueryHeader'),

												 'proxy_host' => CONTROL_PROXY_NAME,

												 'proxy_port' => CONTROL_PROXY_PORT,

												 'proxy_login' => CONTROL_PROXY_LOGIN,

												 'proxy_password' => CONTROL_PROXY_PASSWORD

											   )

										 );

		}

		else

		{

			$this->soap = new SoapClient($this->wsdl_file_url, 

										 array('soap_version' => SOAP_1_2,

												 'exceptions' => 0,

												 'classmap' => array('QAAuthentication' => 'QAAuthentication',

												 				     'QAQueryHeader' => 'QAQueryHeader')

											  )

										 );			

		}

		

		if(is_soap_fault($this->soap))

		{

			$this->soap = NULL;

		}

	}

	

	/**

	 * Build authentication header

	 */

	private function build_auth_header()

	{

    $b = new QAQueryHeader($this->username, $this->password);

		

		$authHeader = new SoapHeader(self::SOAP_HEADER_NAMESPACE, 'QAQueryHeader', $b);

		

		$this->soap->__setSoapHeaders(array($authHeader));

	}

	

	/**

	 * Check soap

	 * @param object $soapResult

	 * @throws Exception

	 * @return object

	 */

	private function check_soap($soapResult)

	{

		if(is_soap_fault($soapResult))

		{

			$err = "QAS SOAP Fault - " . "Code: {" . $soapResult->faultcode . "}, " . "Description: {"

                . $soapResult->faultstring . "}";

            

            error_log($err, 0);

            

            $soapResult = NULL;

            throw new Exception($err);

		}

		

		return ($soapResult);

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::Search()

	 */

	public function Search($search, $countryId, $engine, $flatten, $intensity, $promptset, $threshold, $timeout, $layout, $formattedAddressInPicklist, $requestTag, $localisation)

	{

		$engineOptions = array("_" => $engine, self::PARAM_FLATTEN => $flatten);

		

		if(NULL != $promptset)

		{

			$engineOptions[self::PARAM_PROMPTSET] = $promptset;

		}

		

		if(0 != $threshold)

		{

			$engineOptions[self::PARAM_THRESHOLD] = $threshold;

		}

		

		if(-1 != $timeout)

		{

			$engineOptions[self::PARAM_TIMEOUT] = $timeout;

		}

		

		$args = array(self::PARAM_COUNTRY => $countryId, self::PARAM_SEARCH => $search, self::PARAM_ENGINE => $engineOptions);

		

		if(NULL != $layout)

		{

			$args[self::PARAM_LAYOUT] = $layout;	

		}

		

		if(NULL != $requestTag)

		{

			$args[self::PARAM_REQUEST_TAG] = $requestTag;

		}

		

		$this->build_auth_header();

		

		$result = $this->check_soap($this->soap->DoSearch($args));

		

		if(NULL != $result)

		{

			$result = new SearchResult($result);

		}

				

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::Refine()

	 */

	public function Refine($moniker, $refinement, $layout, $formattedAddressInPicklist, $threshold, $timeout, $requestTag, $localisation)

	{

		$args = array(self::PARAM_MONIKER => $moniker, self::PARAM_REFINEMENT => $refinement);

		

		if(0 != $threshold)

		{

			$args[self::PARAM_THRESHOLD] = $threshold;

		}

		

		if(-1 != $timeout)

		{

			$args[self::PARAM_TIMEOUT] = $timeout;

		}

		

		if(NULL != $requestTag)

		{

			$args[self::PARAM_REQUEST_TAG] = $requestTag;

		}

		

		$this->build_auth_header();

		

		$result = $this->check_soap($this->soap->DoRefine($args));

		

		if(NULL != $result)

		{

			$result = new Picklist($result->QAPicklist);

		}

		

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::GetAddress()

	 */

	public function GetAddress($moniker, $layout, $requestTag, $localisation)

	{

		$args = array(self::PARAM_LAYOUT => $layout, self::PARAM_MONIKER => $moniker);

		

		if(NULL != $requestTag)

		{

			$args[self::PARAM_REQUEST_TAG] = $requestTag;

		}

		

		$this->build_auth_header();

		

		$result = $this->check_soap($this->soap->DoGetAddress($args));

		

		if(NULL != $result)

		{

			$result = new FormattedAddress($result->QAAddress);

		}

				

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::GetData()

	 */

	public function GetData($localisation)

	{

		$this->build_auth_header();

		

		$result = $this->check_soap($this->soap->DoGetData());

		

		if($result != NULL)

		{

			$result = Dataset::CreateArray($result);

		}

		

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::GetLicenseInfo()

	 */

	public function GetLicenseInfo($localisation)

	{

		$this->build_auth_header();

		$result = $this->check_soap($this->soap->DoGetLicenseInfo());

		

		if(NULL != $result)

		{

			if(is_array($result->LicensedSet))

			{

				return $result->LicensedSet;

			}

			else

			{

				return array($result->LicensedSet);

			}

		}

		

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::GetSystemInfo()

	 */

	public function GetSystemInfo($localisation)

	{

		$this->build_auth_header();

		$result = $this->check_soap($this->soap->DoGetSystemInfo());

		

		if(NULL != $result)

		{

			if(is_array($result->SystemInfo))

			{

				return $result->SystemInfo;

			}

			else

			{

				return array($result->SystemInfo);

			}

		}

		

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::GetDataMapDetail()

	 */

	public function GetDataMapDetail($countryId, $localisation)

	{

		$args = array(self::PARAM_COUNTRY => $countryId);

		

		$this->build_auth_header();

		

		$result = $this->check_soap($this->soap->DoGetDataMapDetail($args));

		

		if(NULL != $result)

		{

			$result = LicensedSet::CreateArray($result);	

		}

		

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::GetExampleAddresses()

	 */

	public function GetExampleAddresses($countryId, $layout, $requestTag, $localisation)

	{

		$args = array(self::PARAM_COUNTRY => $countryId, self::PARAM_LAYOUT => $layout);

		

		// Set request tag if supplied

		if(NULL != $requestTag)

		{

			$args[self::PARAM_REQUEST_TAG] = $requestTag;

		}

		

		$this->build_auth_header();

		

		$result = $this->check_soap($this->soap->DoGetExampleAddresses($args));

		

		if(NULL != $result)

		{

			$result = ExampleAddress::CreateArray($result);	

		}

		

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::GetLayouts()

	 */

	public function GetLayouts($countryId, $localisation)

	{

		$args = array(self::PARAM_COUNTRY => $countryId);

		

		$this->build_auth_header();

		

		$result = $this->check_soap($this->soap->DoGetLayouts($args));

		

		if($result != NULL)

		{			

			$result = Layout::CreateArray($result);

		}		

		

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::GetPromptSet()

	 */

	public function GetPromptSet($countryId, $engine, $flatten, $intensity, $promptset, $threshold, $timeout, $localisation)

	{	

		$args = array(self::PARAM_COUNTRY => $countryId, self::PARAM_PROMPTSET => $promptset, self::PARAM_ENGINE => $engine);

		

		$this->build_auth_header(); 

		

		$result = $this->check_soap($this->soap->DoGetPromptSet($args));

		

		if(NULL != $result)

		{

			$result = new PromptSet($result);

		}

				

		return $result;

	}

	

	/* (non-PHPdoc)

	 * @see IQASCapture::CanSearch()

	 */

	public function CanSearch($countryId, $engine, $flatten, $intensity, $promptset, $threshold, $timeout, $layout, $localisation)

	{

		$engineOptions = array("_" => $engine, self::PARAM_FLATTEN => $flatten, self::PARAM_PROMPTSET => $promptset);

		

		$args = array(self::PARAM_COUNTRY => $countryId, self::PARAM_ENGINE => $engineOptions, self::PARAM_FLATTEN => $flatten);

		

		if(NULL != $layout)

		{

			$args[self::PARAM_LAYOUT] = $layout;

		}

		

		$this->build_auth_header(); 



		$result = $this->check_soap($this->soap->DoCanSearch($args));

		

		if(NULL != $result)

		{

			$result = new CanSearch($result);

		}

				

		return $result;

	}

	

	/**

	 * 

	 * Get fault string

	 * @param string $sFault

	 * @return string

	 */	

    public function GetFaultString($sFault)

    {

        if ((!is_string($sFault) || $sFault == "") && ($this->getSoapFault() != NULL))

            return ("[" . $this->getSoapFault() . "]");

        else

            return ($sFault);

    }

    

	/**

	 * Get SOAP fault

	 * @return string

	 */	

    private function getSoapFault() 

    {

        return (isset($this->soap->__soap_fault) ? $this->soap->__soap_fault->faultstring : NULL); 

    }

}