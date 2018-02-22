<?php
/** ---------------------------------------------------------------------
 * app/lib/core/Plugins/InformationService/Wikipedia.php :
 * ----------------------------------------------------------------------
 * Iconclass Plufin by Karl Becker 2017
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2015 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * @package CollectiveAccess
 * @subpackage InformationService
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */

  /**
    *
    */ 
    
    
require_once(__CA_LIB_DIR__."/core/Plugins/IWLPlugInformationService.php");
require_once(__CA_LIB_DIR__."/core/Plugins/InformationService/BaseInformationServicePlugin.php");

global $g_information_service_settings_Iconclass;
$g_information_service_settings_Iconclass = array();

class WLPlugInformationServiceIconclass Extends BaseInformationServicePlugin Implements IWLPlugInformationService {
	# ------------------------------------------------
	static $s_settings;
	# ------------------------------------------------
	/**
	 *
	 */
	public function __construct() {
		global $g_information_service_settings_Iconclass;

		WLPlugInformationServiceIconclass::$s_settings = $g_information_service_settings_Iconclass;
		parent::__construct();
		$this->info['NAME'] = 'Iconclass';
		
		$this->description = _t('Provides access to Iconclass service');
	}
	# ------------------------------------------------
	/** 
	 * Get all settings settings defined by this plugin as an array
	 *
	 * @return array
	 */
	public function getAvailableSettings() {
		return WLPlugInformationServiceIconclass::$s_settings;
	}
	# ------------------------------------------------
	# Data
	# ------------------------------------------------
	/** 
	 * Perform lookup on Iconclass-based data service
	 *
	 * @param array $pa_settings Plugin settings values
	 * @param string $ps_search The expression with which to query the remote data service
	 * @param array $pa_options Lookup options (none defined yet)
	 * @return array
	 */
	public function lookup($pa_settings, $ps_search, $pa_options=null) {

		$vs_content = caQueryExternalWebservice(
            $vs_url = 'http://iconclass.org/rkd/9/?q='.urlencode($ps_search).'&q_s=1&fmt=json'
		);

		$va_content = @json_decode($vs_content, true);
		if(!isset($va_content['records']) || !is_array($va_content['records'])) { return array(); }

		// the top two levels are 'diagnostic' and 'records'
		$va_results = $va_content['records'];
		$va_return = array();

		foreach($va_results as $va_result) {

			$va_return['results'][] = array(
				'label' => $va_result['txt']['de'],
				'url' => 'http://iconclass.org/'.$va_result['n'],
				'idno' => $va_result['n'],
			);
		}

		return $va_return;
	}
	# ------------------------------------------------
	/** 
	 * Fetch details about a specific item from a Iconclass-based data service for "more info" panel
	 *
	 * @param array $pa_settings Plugin settings values
	 * @param string $ps_url The URL originally returned by the data service uniquely identifying the item
	 * @return array An array of data from the data server defining the item.
	 */
	public function getExtendedInformation($pa_settings, $ps_url) {
		$vs_display = "<p><a href='$ps_url' target='_blank'>$ps_url</a></p>";

		return array('display' => $vs_display);
	}
	# ------------------------------------------------
}
