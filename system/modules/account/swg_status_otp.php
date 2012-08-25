<?php
//j// BOF

/*n// NOTE
----------------------------------------------------------------------------
secured WebGine
net-based application engine
----------------------------------------------------------------------------
(C) direct Netware Group - All rights reserved
http://www.direct-netware.de/redirect.php?swg

The following license agreement remains valid unless any additions or
changes are being made by direct Netware Group in a written form.

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
more details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc.,
59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
----------------------------------------------------------------------------
http://www.direct-netware.de/redirect.php?licenses;gpl
----------------------------------------------------------------------------
#echo(sWGaccountOtpLoginVersion)#
sWG/#echo(__FILEPATH__)#
----------------------------------------------------------------------------
NOTE_END //n*/
/**
* account/swg_status_otp.php
*
* @internal   We are using phpDocumentor to automate the documentation process
*             for creating the Developer's Manual. All sections including
*             these special comments will be removed from the release source
*             code.
*             Use the following line to ensure 76 character sizes:
* ----------------------------------------------------------------------------
* @author     direct Netware Group
* @copyright  (C) direct Netware Group - All rights reserved
* @package    sWG
* @subpackage account_otp
* @since      v0.1.00
* @license    http://www.direct-netware.de/redirect.php?licenses;gpl
*             GNU General Public License 2
*/

/* -------------------------------------------------------------------------
All comments will be removed in the "production" packages (they will be in
all development packets)
------------------------------------------------------------------------- */

//j// Basic configuration

/* -------------------------------------------------------------------------
Direct calls will be honored with an "exit ()"
------------------------------------------------------------------------- */

if (!defined ("direct_product_iversion")) { exit (); }

//j// Script specific commands

if (!isset ($direct_settings['account_https_otp_list'])) { $direct_settings['account_https_otp_list'] = false; }
if (!isset ($direct_settings['account_otp_list_lifetime'])) { $direct_settings['account_otp_list_lifetime'] = 31536000; }
if (!isset ($direct_settings['account_otp'])) { $direct_settings['account_otp'] = false; }
if (!isset ($direct_settings['account_otp_password_entries'])) { $direct_settings['account_otp_password_entries'] = 26; }
if (!isset ($direct_settings['account_otp_password_min'])) { $direct_settings['account_otp_password_min'] = 10; }
if (!isset ($direct_settings['account_otp_position_max'])) { $direct_settings['account_otp_position_max'] = 100; }
if (!isset ($direct_settings['account_password_bytemix'])) { $direct_settings['account_password_bytemix'] = ($direct_settings['swg_id'] ^ (strrev ($direct_settings['swg_id']))); }
if (!isset ($direct_settings['serviceicon_default_back'])) { $direct_settings['serviceicon_default_back'] = "mini_default_back.png"; }
if (!isset ($direct_settings['users_password_min'])) { $direct_settings['users_password_min'] = 6; }

if ($direct_settings['a'] == "index") { $direct_settings['a'] = "list"; }
//j// BOS
switch ($direct_settings['a'])
{
//j// ($direct_settings['a'] == "list")||($direct_settings['a'] == "list-save")
case "list":
case "list-save":
{
	$g_mode_save = (($direct_settings['a'] == "list-save") ? true : false);
	if (USE_debug_reporting) { direct_debug (1,"sWG/#echo(__FILEPATH__)# _a={$direct_settings['a']}_ (#echo(__LINE__)#)"); }

	if ($g_mode_save)
	{
		$direct_cachedata['page_this'] = "";
		$direct_cachedata['page_backlink'] = "m=account;s=status_otp;a=list";
		$direct_cachedata['page_homelink'] = "m=account;a=services";
	}
	else
	{
		$direct_cachedata['page_this'] = "m=account;s=status_otp;a=list";
		$direct_cachedata['page_backlink'] = "m=account;a=services";
		$direct_cachedata['page_homelink'] = $direct_cachedata['page_backlink'];
	}

	if ($direct_globals['kernel']->serviceInitDefault ())
	{
	if ($direct_settings['account_otp'])
	{
	if ($direct_globals['kernel']->vUsertypeGetInt ($direct_settings['user']['type']))
	{
	//j// BOA
	if ($g_mode_save) { $direct_globals['output']->relatedManager ("account_status_otp_list_form_save","pre_module_service_action"); }
	else
	{
		$direct_globals['output']->relatedManager ("account_status_otp_list_form","pre_module_service_action");
		$direct_globals['kernel']->serviceHttps ($direct_settings['account_https_otp_list'],$direct_cachedata['page_this']);
	}

	$direct_globals['basic_functions']->requireClass ('dNG\sWG\directFormbuilder');
	$direct_globals['basic_functions']->requireFile ($direct_settings['path_system']."/functions/swg_evar_storager.php");
	direct_local_integration ("account");

	direct_class_init ("formbuilder");
	$direct_globals['output']->servicemenu ("account_otp");
	$direct_globals['output']->optionsInsert (2,"servicemenu","m=account;a=services",(direct_local_get ("core_back")),$direct_settings['serviceicon_default_back'],"url0");

	if ($direct_settings['account_otp_password_min'] < $direct_settings['users_password_min']) { $direct_settings['account_otp_password_min'] = $direct_settings['users_password_min']; }
	$g_otp_array = direct_evar_storage_get ($direct_settings['user']['id'],"e268443e43d93dab7ebef303bbe9642f","otp_list");
	// md5 ("account")

	if ($g_mode_save) { $direct_cachedata['i_aotp_entries'] = (isset ($GLOBALS['i_aotp_entries']) ? ($direct_globals['basic_functions']->inputfilterNumber ($GLOBALS['i_aotp_entries'])) : ""); }
	else { $direct_cachedata['i_aotp_entries'] = $direct_settings['account_otp_password_entries']; }

/* -------------------------------------------------------------------------
Build the form
------------------------------------------------------------------------- */

	if ((!$g_mode_save)&&($g_otp_array))
	{
		$g_form_section = direct_local_get ("account_otp_list_current");

		$direct_globals['formbuilder']->entryAdd ("info",(array ("section" => $g_form_section,"name" => "ainfo_1","title" => direct_local_get ("account_otp_list_remaining_entries"),"content" => count ($g_otp_array['account_otp_list']))));

		if (isset ($g_otp_array['account_otp_failed_stats']))
		{
			$direct_globals['formbuilder']->entryAdd ("info",(array ("section" => $g_form_section,"name" => "ainfo_2","title" => direct_local_get ("account_otp_wrong_passwords"),"content" => $g_otp_array['account_otp_failed_stats']['wrong_passwords'])));
			$direct_globals['formbuilder']->entryAdd ("info",(array ("section" => $g_form_section,"name" => "ainfo_3","title" => direct_local_get ("account_otp_latest_wrong_password"),"content" => $direct_globals['basic_functions']->datetime ("longdate&time",$g_otp_array['account_otp_failed_stats']['latest'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect"))))));
		}
	}

	$direct_globals['formbuilder']->entryAddNumber (array ("section" => direct_local_get ("account_otp_list_new"),"name" => "aotp_entries","title" => direct_local_get ("account_otp_list_entries"),"required" => true,"size" => "s","min" => 10,"max" => $direct_settings['account_otp_position_max']));
	$direct_cachedata['output_formelements'] = $direct_globals['formbuilder']->formGet ($g_mode_save);

	if (($g_mode_save)&&($direct_globals['formbuilder']->check_result))
	{
/* -------------------------------------------------------------------------
Save data edited
------------------------------------------------------------------------- */

		$direct_globals['output']->themeSubtype ("printview");

		$direct_cachedata['output_otp_list'] = array ();
		$g_update_check = ($g_otp_array ? true : false);
		$g_otp_array = array ("account_otp_list" => array ());

		for ($g_i = 0;$g_i < $direct_cachedata['i_aotp_entries'];$g_i++)
		{
			$g_otp_password = $direct_globals['basic_functions']->tmd5 (mt_rand ());
			$g_otp_password_offset = mt_rand (0,(96 - $direct_settings['account_otp_password_min']));
			$g_otp_password = substr ($g_otp_password,$g_otp_password_offset,$direct_settings['account_otp_password_min']);

			$direct_cachedata['output_otp_list'][] = $g_otp_password;
			$g_otp_array['account_otp_list'][] = $direct_globals['basic_functions']->tmd5 ($g_otp_password,$direct_settings['account_password_bytemix']);
		}

		direct_evar_storage_write ($g_otp_array,$direct_settings['user']['id'],"e268443e43d93dab7ebef303bbe9642f","otp_list",$direct_cachedata['core_time'],($direct_cachedata['core_time'] + $direct_settings['account_otp_list_lifetime']),$g_update_check);
		// md5 ("account")

		$direct_globals['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']);
		$direct_globals['output']->relatedManager ("account_status_otp_list_form_save","post_module_service_action");
		$direct_globals['output']->oset ("account","otp_list");
		$direct_globals['output']->outputSend (direct_local_get ("account_otp_list_new"));
	}
	else
	{
/* -------------------------------------------------------------------------
View form
------------------------------------------------------------------------- */

		$direct_cachedata['output_formbutton'] = direct_local_get ("account_otp_list_new_generate");
		$direct_cachedata['output_formtarget'] = "m=account;s=status_otp;a=list-save";
		$direct_cachedata['output_formtitle'] = direct_local_get ("account_otp_list");

		$direct_globals['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']);
		$direct_globals['output']->relatedManager ("account_status_otp_list_form","post_module_service_action");
		$direct_globals['output']->oset ("default","form");
		$direct_globals['output']->outputSend ($direct_cachedata['output_formtitle']);
	}
	//j// EOA
	}
	else { $direct_globals['error_functions']->outputSendError ("login","core_access_denied","","sWG/#echo(__FILEPATH__)# _a={$direct_settings['a']}_ (#echo(__LINE__)#)"); }
	}
	else { $direct_globals['error_functions']->outputSendError ("standard","core_service_inactive","","sWG/#echo(__FILEPATH__)# _a={$direct_settings['a']}_ (#echo(__LINE__)#)"); }
	}

	$direct_cachedata['core_service_activated'] = true;
	break 1;
}
//j// EOS
}

//j// EOF
?>