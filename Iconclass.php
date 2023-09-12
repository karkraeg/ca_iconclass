<?php
/** ---------------------------------------------------------------------
 * app/lib/Plugins/InformationService/Iconclass.php :
 * ----------------------------------------------------------------------
 * Iconclass InformationService by Karl Krägelin 2017-2023
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2015-2018 Whirl-i-Gig
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


require_once(__CA_LIB_DIR__."/Plugins/IWLPlugInformationService.php");
require_once(__CA_LIB_DIR__."/Plugins/InformationService/BaseInformationServicePlugin.php");

global $g_information_service_settings_Iconclass;
$g_information_service_settings_Iconclass = [
    'numberOfresults' => array(
        'formatType' => FT_TEXT,
        'displayType' => DT_FIELD,
        'default' => 6,
        'width' => 10,
        'height' => 1,
        'label' => _t('Number of results'),
        'validForRootOnly' => 1,
        'description' => _t('Enter an integer value for my setting.'),
    ),

];

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
        global $g_ui_locale;
		if ($vs_locale = ($g_ui_locale) ? $g_ui_locale : __CA_DEFAULT_LOCALE__) {
			$vs_lang = strtolower(array_shift(explode("_", $vs_locale)));
		} else {
			$vs_lang = 'en';
		}
        $va_number_of_results = $pa_settings['numberOfresults'];
        // query the Iconclass API
		$vs_content = caQueryExternalWebservice(
            $vs_url = 'https://iconclass.org/api/search?q='.urlencode($ps_search).'&lang='.$vs_lang.'&size=999&page=1&sort=rank&keys=0'
		);
        // decode the JSON response
		$va_content = @json_decode($vs_content, true);
		// check if $va_content has a key called "records" and if the value of that key is an array. If either of these conditions is not true, return an empty array.
        if(!isset($va_content['result']) || !is_array($va_content['result'])) { return []; }

		// extract the value of the "result" key from an associative array $va_content and assigns it to a new variable $va_results.
		$va_results = array_slice($va_content['result'], 0, $va_number_of_results);
		// initialize a new empty array $va_return.
        $va_return = [];

		foreach($va_results as $va_result) {
			// for each id in $va_results call 'https://iconclass.org/'.$va_result.'.json'
            // and assign the result to a new variable $vs_content.
            $id_content = caQueryExternalWebservice($vs_url = 'https://iconclass.org/'.$va_result.'.json');
            $id_content_decoded = @json_decode($id_content, true);

            $va_return['results'][] = [
				'label' => $id_content_decoded['txt']['de'],
				'url' => 'http://iconclass.org/de/'.$va_result,
				'idno' => $va_result,
            ];
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
		$vs_display = "<p><a href='{$ps_url}' target='_blank'>{$ps_url}</a></p>";

		return ['display' => $vs_display];
	}
	# ------------------------------------------------
}
