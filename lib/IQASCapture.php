<?php
/**

 * Interface for Quick Address

 * @author aikkeongt

 *

 */

interface IQASCapture

{

	/**

	 * Search

	 * @param string $search

	 * @param string $countryId

	 * @param string $engine

	 * @param bool $flatten

	 * @param int $intensity

	 * @param string $promptset

	 * @param int $threshold

	 * @param int $timeout

	 * @param string $layout

	 * @param bool $formattedAddressInPicklist

	 * @param string $requestTag

	 * @param string $localisation

	 */

	public function Search($search, $countryId, $engine, $flatten, $intensity, $promptset, $threshold, $timeout, $layout, $formattedAddressInPicklist, $requestTag, $localisation);

		

	/**

	 * Refine

	 * @param string $moniker

	 * @param string $refinement

	 * @param string $layout

	 * @param bool $formattedAddressInPicklist

	 * @param int $threshold

	 * @param int $timeout

	 * @param string $requestTag

	 * @param string $localisation

	 */

	public function Refine($moniker, $refinement, $layout, $formattedAddressInPicklist, $threshold, $timeout, $requestTag, $localisation);

	

	/**

	 * Get Address

	 * @param string $moniker

	 * @param string $layout

	 * @param string $requestTag

	 * @param string $localisation

	 */

	public function GetAddress($moniker, $layout, $requestTag, $localisation);

	

	/**

	 * Can Search

	 * @param string $countryId

	 * @param string $engine

	 * @param bool $flatten

	 * @param int $intensity

	 * @param string $promptset

	 * @param int $threshold

	 * @param int $timeout

	 * @param string $layout

	 * @param string $localisation

	 */

	public function CanSearch($countryId, $engine, $flatten, $intensity, $promptset, $threshold, $timeout, $layout, $localisation);

		

	/**

	 * Get Data

	 * @param string $localisation

	 */

	public function GetData($localisation);

	

	/**

	 * Get License Info

	 * @param string $localisation

	 */

	public function GetLicenseInfo($localisation);

	

	/**

	 * Get Datamap Detail

	 * @param string $countryId

	 * @param string $localisation

	 */

	public function GetDataMapDetail($countryId, $localisation);

	

	/**

	 * Get Example Addresses

	 * @param string $countryId

	 * @param string $layout

	 * @param string $requestTag

	 * @param string $localisation

	 */

	public function GetExampleAddresses($countryId, $layout, $requestTag, $localisation);

	

	/**

	 * Get Layouts

	 * @param string $countryId

	 * @param string $localisation

	 */

	public function GetLayouts($countryId, $localisation);

	

	/**

	 * Get PromptSet

	 * @param string $countryId

	 * @param string $engine

	 * @param bool $flatten

	 * @param int $intensity

	 * @param string $promptset

	 * @param int $threshold

	 * @param int $timeout

	 * @param string $localisation

	 */

	public function GetPromptSet($countryId, $engine, $flatten, $intensity, $promptset, $threshold, $timeout, $localisation);

	

	/**

	 * Get System Info

	 * @param string $localisation

	 */

	public function GetSystemInfo($localisation);	

}