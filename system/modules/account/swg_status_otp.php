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
* @uses       direct_product_iversion
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
if (!isset ($direct_settings['account_otp_password_bytemix'])) { $direct_settings['account_otp_password_bytemix'] = ($direct_settings['swg_id'] ^ (strrev ($direct_settings['swg_id']))); }
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
		$direct_cachedata['page_backlink'] = "m=account&s=status_otp&a=list";
		$direct_cachedata['page_homelink'] = "m=account&a=services";
	}
	else
	{
		$direct_cachedata['page_this'] = "m=account&s=status_otp&a=list";
		$direct_cachedata['page_backlink'] = "m=account&a=services";
		$direct_cachedata['page_homelink'] = $direct_cachedata['page_backlink'];
	}

	if ($direct_classes['kernel']->service_init_default ())
	{
	if ($direct_settings['account_otp'])
	{
	if ($direct_classes['kernel']->v_usertype_get_int ($direct_settings['user']['type']))
	{
	//j// BOA
	if ($g_mode_save) { direct_output_related_manager ("account_status_otp_list_form_save","pre_module_service_action"); }
	else
	{
		direct_output_related_manager ("account_status_otp_list_form","pre_module_service_action");
		$direct_classes['kernel']->service_https ($direct_settings['account_https_otp_list'],$direct_cachedata['page_this']);
	}

	$direct_classes['basic_functions']->require_file ($direct_settings['path_system']."/classes/swg_formbuilder.php");
	$direct_classes['basic_functions']->require_file ($direct_settings['path_system']."/functions/swg_tmp_storager.php");
	direct_local_integration ("account");

	direct_class_init ("formbuilder");
	direct_class_init ("output");
	$direct_classes['output']->servicemenu ("account_otp");
	$direct_classes['output']->options_insert (2,"servicemenu","m=account&a=services",(direct_local_get ("core_back")),$direct_settings['serviceicon_default_back'],"url0");

	if ($direct_settings['account_otp_password_min'] < $direct_settings['users_password_min']) { $direct_settings['account_otp_password_min'] = $direct_settings['users_password_min']; }
	$g_otp_array = direct_tmp_storage_get ("evars",$direct_settings['user']['id'],"e268443e43d93dab7ebef303bbe9642f","otp_list");
	// md5 ("account")

	if ($g_mode_save) { $direct_cachedata['i_aotp_entries'] = (isset ($GLOBALS['i_aotp_entries']) ? ($direct_classes['basic_functions']->inputfilter_number ($GLOBALS['i_aotp_entries'])) : ""); }
	else { $direct_cachedata['i_aotp_entries'] = $direct_settings['account_otp_password_entries']; }

	if ($g_otp_array)
	{
		$direct_cachedata['i_ainfo_1'] = count ($g_otp_array);

		if (isset ($g_otp_array['account_otp_failed_stats']))
		{
			$direct_cachedata['i_ainfo_1']--;
			$direct_cachedata['i_ainfo_2'] = $g_otp_array['account_otp_failed_stats']['wrong_passwords'];
			$direct_cachedata['i_ainfo_3'] = $direct_classes['basic_functions']->datetime ("longdate&time",$g_otp_array['account_otp_failed_stats']['latest'],$direct_settings['user']['timezone'],(direct_local_get ("datetime_dtconnect")));
		}
	}

/* -------------------------------------------------------------------------
Build the form
------------------------------------------------------------------------- */

	if ($g_otp_array)
	{
		$direct_classes['formbuilder']->entry_add ("subtitle","otp_list_current",(direct_local_get ("account_otp_list_current")));
		$direct_classes['formbuilder']->entry_add ("info","ainfo_1",(direct_local_get ("account_otp_list_remaining_entries")));

		if (isset ($g_otp_array['account_otp_failed_stats']))
		{
			$direct_classes['formbuilder']->entry_add ("info","ainfo_2",(direct_local_get ("account_otp_wrong_passwords")));
			$direct_classes['formbuilder']->entry_add ("info","ainfo_3",(direct_local_get ("account_otp_latest_wrong_password")));
		}
	}

	$direct_classes['formbuilder']->entry_add ("subtitle","otp_list_new",(direct_local_get ("account_otp_list_new")));
	$direct_classes['formbuilder']->entry_add_number ("aotp_entries",(direct_local_get ("account_otp_list_entries")),true,"s",10,$direct_settings['account_otp_position_max']);

	$direct_cachedata['output_formelements'] = $direct_classes['formbuilder']->form_get ($g_mode_save);

	if (($g_mode_save)&&($direct_classes['formbuilder']->check_result))
	{
/* -------------------------------------------------------------------------
Save data edited
------------------------------------------------------------------------- */

		direct_output_theme_subtype ("printview");

		$direct_cachedata['output_otp_list'] = array ();
		$g_otp_array = array ();

		for ($g_i = 0;$g_i < $direct_cachedata['i_aotp_entries'];$g_i++)
		{
			$g_otp_password = $direct_classes['basic_functions']->tmd5 (mt_rand ());
			$g_otp_password_offset = mt_rand (0,(96 - $direct_settings['account_otp_password_min']));
			$g_otp_password = substr ($g_otp_password,$g_otp_password_offset,$direct_settings['account_otp_password_min']);

			$direct_cachedata['output_otp_list'][] = $g_otp_password;
			$g_otp_array[] = $direct_classes['basic_functions']->tmd5 ($g_otp_password,$direct_settings['account_password_bytemix']);
		}

		direct_tmp_storage_write ($g_otp_array,$direct_settings['user']['id'],"e268443e43d93dab7ebef303bbe9642f","otp_list","evars",$direct_cachedata['core_time'],($direct_cachedata['core_time'] + $direct_settings['account_otp_list_lifetime']));
		// md5 ("account")

		direct_output_related_manager ("account_status_otp_list_form_save","post_module_service_action");
		$direct_classes['output']->oset ("account","otp_list");
		$direct_classes['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']);
		$direct_classes['output']->page_show (direct_local_get ("account_otp_list_new"));
	}
	else
	{
/* -------------------------------------------------------------------------
View form
------------------------------------------------------------------------- */

		$direct_cachedata['output_formbutton'] = direct_local_get ("account_otp_list_new_generate");
		$direct_cachedata['output_formtarget'] = "m=account&s=status_otp&a=list-save";
		$direct_cachedata['output_formtitle'] = direct_local_get ("account_otp_list");

		direct_output_related_manager ("account_status_otp_list_form","post_module_service_action");
		$direct_classes['output']->oset ("default","form");
		$direct_classes['output']->header (false,true,$direct_settings['p3p_url'],$direct_settings['p3p_cp']);
		$direct_classes['output']->page_show ($direct_cachedata['output_formtitle']);
	}
	//j// EOA
	}
	else { $direct_classes['error_functions']->error_page ("login","core_access_denied","sWG/#echo(__FILEPATH__)# _a={$direct_settings['a']}_ (#echo(__LINE__)#)"); }
	}
	else { $direct_classes['error_functions']->error_page ("standard","core_service_inactive","sWG/#echo(__FILEPATH__)# _a={$direct_settings['a']}_ (#echo(__LINE__)#)"); }
	}

	$direct_cachedata['core_service_activated'] = true;
	break 1;
}
//j// EOS
}

//j// EOF
?>