<?php
// namespace LLR;


/**

 * Address Line

 * @author aikkeongt

 *

 */

class AddressLine

{

	public $Label;

	public $Line;

	public $DataplusGroup = null;

	public $LineType;

	public $IsTruncated;	

	public $IsOverflow;

	

	/**

	 * Constructor

	 * @param object $result

	 */

	public function AddressLine($result)

	{

		$this->Label = $result->Label;

		$this->Line = $result->Line;

		$this->LineType = $result->LineContent;

		$this->IsOverflow = $result->Overflow;

		$this->IsTruncated = $result->Truncated;

		

		if(isset($result->DataplusGroup) && null != $result->DataplusGroup)

		{

			$this->DataplusGroup = array();

			if (is_array($result->DataplusGroup))

			{

				foreach ($result->DataplusGroup AS $dataplusGroup)

				{

					array_push($this->DataplusGroup, new DataplusGroup($dataplusGroup));

				}

			}

			else

			{

				array_push($this->DataplusGroup, new DataplusGroup($dataplusGroup));

			}				

		}

	}

}



/**

 * Can Search

 * @author aikkeongt

 *

 */

class CanSearch

{

	public $IsOk;

	public $Error = 0;

	public $ErrorMessage;

	

	/**

	 * Can Search

	 * @param object $result

	 */

	public function CanSearch($result)

	{

		$this->IsOk = $result->IsOk;

		if(isset($result->ErrorCode))

		{

			$this->Error = $result->ErrorCode;

		}

		

		if(isset($result->ErrorMessage) && null != $result->ErrorMessage)

		{

			$this->ErrorMessage = $result->ErrorMessage;

		}

	}

}



/**

 * Dataplus Group

 * @author aikkeongt

 *

 */

class DataplusGroup

{

	public $Name;

	public $Items;

	

	/**

	 * Constructor

	 * @param object $result

	 */

	public function DataplusGroup($result)

	{

		$this->Name = $result->GroupName;

		if(isset($result->DataplusGroupItem) && null != $result->DataplusGroupItem)

		{

			$this->Items =$result->DataplusGroupItem;	

		}

	}

}



/**

 * Dataset

 * @author aikkeongt

 *

 */

class Dataset

{

	public $ID;

	public $Name;

	

	/**

	 * Constructor

	 * @param object $result

	 */

	public function Dataset($result)

	{

		$this->ID = $result->ID;

		$this->Name = $result->Name;

	}

	

	/**

	 * Create Array

	 * @param object $results

	 * @return array

	 */

	public static function CreateArray($results)

	{

		$aResults = array();

		

		if (NULL != $results)

		{

			if (is_array($results->DataSet))

			{

				foreach ($results->DataSet AS $dataset)

				{

					array_push($aResults, new Dataset($dataset));

				}

			}

			else

			{

				array_push($aResults, new Layout($results->DataSet));

			}

		}

		

		return $aResults;

	}

}



/**

 * Example Address

 * @author aikkeongt

 *

 */

class ExampleAddress

{

	public $Address;

	public $Comment;

	

	/**

	 * Constructor

	 * @param object $result

	 */

	public function ExampleAddress($result)

	{

		$this->Comment = $result->Comment;

		$this->Address = new FormattedAddress($result->Address);

	}

	

	/**

	 * Create Array

	 * @param object $results

	 * @return array

	 */	

	public static function CreateArray($results)

	{

		$aResults = array();

		

		if (NULL != $results && isset($results->ExampleAddress))

		{

			if (is_array($results->ExampleAddress))

			{

				foreach ($results->ExampleAddress AS $exampleAddress)

				{

					array_push($aResults, new ExampleAddress($exampleAddress));

				}

			}

			else

			{

				array_push($aResults, new ExampleAddress($results->ExampleAddress));

			}

		}

		

		return $aResults;

	}	

}



/**

 * Formatted Address

 * @author aikkeongt

 *

 */

class FormattedAddress

{

	public $AddressLines = null;

	public $DPVStatus;

	public $IsTruncated;

	public $IsOverFlow;

	

	/**

	 * Constructor

	 * @param object $result

	 */

	public function FormattedAddress($result)

	{

		$this->IsOverFlow = $result->Overflow;

		$this->IsTruncated = $result->Truncated;

		$this->DPVStatus = $result->DPVStatus;

		if(isset($result->AddressLine) && null != $result->AddressLine)

		{

			$this->AddressLines = array();

			if (is_array($result->AddressLine))

			{

				foreach ($result->AddressLine AS $addressLine)

				{

					array_push($this->AddressLines, new AddressLine($addressLine));

				}

			}

			else

			{

				array_push($this->AddressLines, new AddressLine($addressLine));

			}				

		}		

	}

}



/**

 * Layout

 * @author aikkeongt

 *

 */

class Layout

{

	public $Name;

	public $Comment;

	

	/**

	 * Layout

	 * @param unknown_type $result

	 */

	public function Layout($result)

	{

		$this->Name = $result->Name;

		$this->Comment =$result->Comment;

	}

	

	/**

	 * Create Array

	 * @param object $results

	 * @return array

	 */	

	public static function CreateArray($results)

	{

		$aResults = array();

		

		if (NULL != $results)

		{

			if (is_array($results->Layout))

			{

				foreach ($results->Layout AS $layout)

				{

					array_push($aResults, new Layout($layout));

				}

			}

			else

			{

				array_push($aResults, new Layout($results->Layout));

			}

		}

		

		return $aResults;

	}

}



/**

 * Licensed Set

 * @author aikkeongt

 *

 */

class LicensedSet

{

	public $ID;

	public $Description;

	public $CopyRight;

	public $Version;

	public $BaseCountry;

	public $Status;

	public $Server;

	public $WarningLevel;

	public $DaysLeft;

	public $DataDaysLeft;

	public $LicenceDaysLeft;

	

	/**

	 * Constructor

	 * @param object $result

	 */

	public function LicensedSet($result)

	{

		$this->ID = $result->ID;

		$this->Description = $result->Description;

		$this->CopyRight = $result->Copyright;

		$this->Version = $result->Version;

		$this->BaseCountry = $result->BaseCountry;

		$this->Status = $result->Status;

		$this->Server = $result->Server;

		$this->WarningLevel = $result->WarningLevel;

		$this->DaysLeft = $result->DaysLeft;

		$this->DataDaysLeft = $result->DataDaysLeft;

		$this->LicenceDaysLeft = $result->LicenceDaysLeft;

	}

	

	/**

	 * Create Array

	 * @param object $results

	 * @return array

	 */	

	public static function CreateArray($results)

	{

		$aResults = array();

		

		if (NULL != $results)

		{

			if (is_array($results->LicensedSet))

			{

				foreach ($results->LicensedSet AS $licenseSet)

				{

					array_push($aResults, new LicensedSet($licenseSet));

				}

			}

			else

			{

				array_push($aResults, new LicensedSet($results->LicensedSet));

			}

		}

		

		return $aResults;

	}

}



/**

 * Picklist

 * @author aikkeongt

 *

 */

class Picklist

{

	public $Moniker;

	public $Items = null;

	public $Prompt;

	public $Total;

	public $IsAutoStepinSafe;

	public $IsAutoStepinPastClose;

	public $IsAutoformatSafe;

	public $IsAutoformatPastClose;

	public $IsLargePotential;

	public $IsMaxMatches;

	public $AreMoreOtherMatches;

	public $IsOverThreshold;

	public $IsTimeout;

	

	/**

	 * Constructor

	 * @param object $results

	 */	

	function Picklist($results)

	{

		$this->Total = $results->Total;

		$this->Moniker = $results->FullPicklistMoniker;

		$this->Prompt = $results->Prompt;

		$this->IsAutoStepinSafe = $results->AutoStepinSafe;

		$this->IsAutoStepinPastClose = $results->AutoStepinPastClose;

		$this->IsAutoformatSafe = $results->AutoFormatSafe;

		$this->IsAutoformatPastClose = $results->AutoFormatPastClose;

		$this->IsLargePotential = $results->LargePotential;

		$this->IsMaxMatches = $results->MaxMatches;

		$this->AreMoreOtherMatches = $results->MoreOtherMatches;

		$this->IsOverThreshold = $results->OverThreshold;

		$this->IsTimeout = $results->Timeout;

		

		if(isset($results->PicklistEntry) && null != $results->PicklistEntry)

		{

			$this->Items = array();

			if (is_array($results->PicklistEntry))

			{

				foreach ($results->PicklistEntry AS $picklistEntry)

				{

					array_push($this->Items, new PicklistItem($picklistEntry));

				}

			}

			else

			{

				array_push($this->Items, new PicklistItem($results->PicklistEntry));

			}			

		}

	}

}



/**

 * Picklist Item

 * @author aikkeongt

 *

 */

class PicklistItem

{

	public $IsAliasMatch;

	public $IsCanStep;

	public $IsCrossBorderMatch;

	public $IsDummyPOBox;

	public $IsEnhancedData;

	public $IsExtendedData;

	public $IsFullAddress;

	public $IsIncompleteAddress;

	public $IsInformation;

	public $IsMultiples;

	public $IsName;

	public $IsPhantomPrimaryPoint;

	public $IsPostcodeRecode;

	public $IsSubsidiaryData;

	public $IsUnresolvableRange;

	public $IsWarnInformation;

	public $Score;

	public $Moniker;

	public $PartialAddress;

	public $Postcode;

	public $Text;

	

	/**

	 * Constructor

	 * @param object $result

	 */

	public function PicklistItem($result)

	{

		$this->Text = $result->Picklist;

		$this->Postcode = $result->Postcode;

		$this->Score = $result->Score;

		$this->Moniker = $result->Moniker;

		$this->PartialAddress = $result->PartialAddress;

		$this->IsFullAddress = $result->FullAddress;

		$this->IsMultiples = $result->Multiples;

		$this->IsCanStep = $result->CanStep;

		$this->IsAliasMatch = $result->AliasMatch;

		$this->IsPostcodeRecode = $result->PostcodeRecoded;

		$this->IsCrossBorderMatch = $result->CrossBorderMatch;

		$this->IsDummyPOBox = $result->DummyPOBox;

		$this->IsName = $result->Name;

		$this->IsInformation = $result->Information;

		$this->IsWarnInformation = $result->WarnInformation;

		$this->IsIncompleteAddress = $result->IncompleteAddr;

		$this->IsUnresolvableRange = $result->UnresolvableRange;

		$this->IsPhantomPrimaryPoint = $result->PhantomPrimaryPoint;

		$this->IsSubsidiaryData = $result->SubsidiaryData;

		$this->IsExtendedData = $result->ExtendedData;

		$this->IsEnhancedData = $result->EnhancedData; 

	}

}



/**

 * Prompt Line

 * @author aikkeongt

 *

 */

class PromptLine

{

	public $Prompt;

	public $Example;

	public $SuggestedInputLength;

	

	/**

	 * Constructor

	 * @param unknown_type $results

	 */

	public function PromptLine($results)

	{

		$this->Prompt = $results->Prompt;

		$this->Example = $results->Example;

		$this->SuggestedInputLength = $results->SuggestedInputLength;

	}

}



/**

 * Prompt Set

 * @author aikkeongt

 *

 */

class PromptSet

{

	public $Lines;

	public $IsDynamic;

	

	/**

	 * Constructor

	 * @param object $results

	 */	

	public function PromptSet($results)

	{

		$this->IsDynamic = $results->Dynamic;

		if(isset($results->Line) && NULL != $results->Line)

		{

			$this->Lines = array();

			if (is_array($results->Line))

			{

				foreach ($results->Line AS $line)

				{

					array_push($this->Lines, new PromptLine($line));

				}

			}

			else

			{

				array_push($this->Lines, new PromptLine($results->Line));

			}				

		}

	}

}



/**

 * Search Result

 * @author aikkeongt

 *

 */

class SearchResult

{

	public $Address;

	public $Picklist;

	public $VerifyLevel;

	

	/**

	 * Constructor

	 * @param object $results

	 */	

	public function SearchResult($results)

	{

		if(isset($results->QAAddress) && NULL != $results->QAAddress)

		{

			$this->Address = new FormattedAddress($results->QAAddress);

		}

		

		if(isset($results->QAPicklist) && NULL != $results->QAPicklist)

		{

			$this->Picklist = new Picklist($results->QAPicklist);

		}



		$this->VerifyLevel = $results->VerifyLevel;

	}

}



/**

 * Authentication class

 * @author aikkeongt

 *

 */

class QAAuthentication

{

	private $Username;

	private $Password;



	/**

	 * Constructor

	 * @param string $username

	 * @param string $password

	 */

	public function __construct($username,$password)

	{

		$this->Username = $username;

		$this->Password = $password;

	}

}



/**

 * Query header class

 * @author aikkeongt

 *

 */

class QAQueryHeader

{

	private $QAAuthentication;

	private $Security;



	/**

	 * Constructor

	 * @param string $username

	 * @param string $password

	 */

	public function __construct($username,$password)

	{

		$this->QAAuthentication = new QAAuthentication($username, $password);

		$this->Security = NULL;

	}

}