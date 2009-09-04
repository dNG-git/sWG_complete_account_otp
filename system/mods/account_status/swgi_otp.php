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
* account_status/swgi_otp.php
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

//j// Functions and classes

//f// direct_mods_account_status_otp_login ($f_data)
/**
* Modification function called by:
* m = account
* s = status
* a = login
*
* @param  array $f_data Array containing call specific data.
* @uses   direct_debug()
* @uses   direct_local_get()
* @uses   direct_output_control::dclass_options_insert()
* @uses   USE_debug_reporting
* @return boolean Always true
* @since  v0.1.00
*/
function direct_mods_account_status_otp_login ($f_data)
{
	global $direct_cachedata,$direct_classes,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login (+f_data)- (#echo(__LINE__)#)"); }

	if ($direct_settings['account_otp'])
	{
		$direct_cachedata['i_aotpposition'] = "";
		$direct_classes['formbuilder']->entry_add ("subtitle","otp_login",(direct_local_get ("account_otp_login")));
		$direct_classes['formbuilder']->entry_add_number ("aotpposition",(direct_local_get ("account_otp_position")),false,"s",0,$direct_settings['account_otp_position_max']);
	}

	return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login ()- (#echo(__LINE__)#)",:#*/true/*#ifdef(DEBUG):,true):#*/;
}

//f// direct_mods_account_status_otp_login_check ($f_data)
/**
* Modification function called by:
* m = account
* s = status
* a = login-save
*
* @param  array $f_data Array containing call specific data.
* @uses   direct_debug()
* @uses   direct_local_get()
* @uses   direct_output_control::dclass_options_insert()
* @uses   USE_debug_reporting
* @return boolean True if the modification is able to process the login
* @since  v0.1.00
*/
function direct_mods_account_status_otp_login_check ($f_data)
{
	global $direct_cachedata,$direct_classes,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login_check (+f_data)- (#echo(__LINE__)#)"); }

	$f_return = ($f_data[0] ? $f_data[0] : false);

	if ($direct_settings['account_otp'])
	{
		$direct_cachedata['i_aotpposition'] = (isset ($GLOBALS['i_aotpposition']) ? ($direct_classes['basic_functions']->inputfilter_basic ($GLOBALS['i_aotpposition'])) : "");

		$direct_classes['formbuilder']->entry_add ("subtitle","otp_login",(direct_local_get ("account_otp_login")));
		$direct_classes['formbuilder']->entry_add_number ("aotpposition",(direct_local_get ("account_otp_position")),false,"s",0,$direct_settings['account_otp_position_max']);

		if ((strlen ($direct_cachedata['i_ausername']))&&(strlen ($direct_cachedata['i_apassword']))&&(is_numeric ($direct_cachedata['i_aotpposition']))) { $f_return = true; }
	}

	return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login_check ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
}

//f// direct_mods_account_status_otp_login_process ($f_data)
/**
* Modification function called by:
* m = account
* s = status
* a = login-save
*
* @param  array $f_data Array containing call specific data.
* @uses   direct_debug()
* @uses   USE_debug_reporting
* @return boolean True if modification login process was successful
* @since  v0.1.00
*/
function direct_mods_account_status_otp_login_process ($f_data)
{
	global $direct_cachedata,$direct_classes,$direct_settings;
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login_process (+f_data)- (#echo(__LINE__)#)"); }

	$f_return = ($f_data[0] ? $f_data[0] : false);

	if (($direct_settings['account_otp'])&&(!$f_return)&&(is_array ($f_data[1]))&&($direct_classes['basic_functions']->include_file ($direct_settings['path_system']."/functions/swg_tmp_storager.php")))
	{
		$f_otp_array = direct_tmp_storage_get ("evars",$f_data[1]['ddbusers_id'],"e268443e43d93dab7ebef303bbe9642f","otp_list");
		// md5 ("account")

		if ((is_array ($f_otp_array))&&(isset ($f_otp_array[$direct_cachedata['i_aotpposition']])))
		{
			$f_otp_password = $f_otp_array[$direct_cachedata['i_aotpposition']];
			unset ($f_otp_array[$direct_cachedata['i_aotpposition']]);
			direct_tmp_storage_write ($f_otp_array,$f_data[1]['ddbusers_id'],"e268443e43d93dab7ebef303bbe9642f","otp_list","evars",$direct_cachedata['core_time'],($direct_cachedata['core_time'] + $direct_settings['account_otp_list_lifetime']));

			if ($direct_cachedata['i_apassword'] == $f_otp_password) { $f_return = true; }
		}
	}

	return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login_process ()- (#echo(__LINE__)#)",:#*/$f_return/*#ifdef(DEBUG):,true):#*/;
}

//f// direct_mods_account_status_otp_login_save ($f_data)
/**
* Modification function called by:
* m = account
* s = status
* a = login-save
*
* @param  array $f_data Array containing call specific data.
* @uses   direct_debug()
* @uses   USE_debug_reporting
* @return boolean True if modification login process was successful
* @since  v0.1.00
*/
function direct_mods_account_status_otp_login_save ($f_data)
{
	if (USE_debug_reporting) { direct_debug (5,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login_save (+f_data)- (#echo(__LINE__)#)"); }

	if ($f_data[0]) { return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login_save ()- (#echo(__LINE__)#)",:#*/$f_data[0]/*#ifdef(DEBUG):,true):#*/; }
	else { return /*#ifdef(DEBUG):direct_debug (7,"sWG/#echo(__FILEPATH__)# -direct_mods_account_status_otp_login_save ()- (#echo(__LINE__)#)",:#*/$f_data[2]/*#ifdef(DEBUG):,true):#*/; }
}

//j// Script specific commands

if (!isset ($direct_settings['account_otp_list_lifetime'])) { $direct_settings['account_otp_list_lifetime'] = 31536000; }
if (!isset ($direct_settings['account_otp'])) { $direct_settings['account_otp'] = false; }
if (!isset ($direct_settings['account_otp_position_max'])) { $direct_settings['account_otp_position_max'] = 100; }
if (!isset ($direct_settings['serviceicon_account_status_otp_login'])) { $direct_settings['serviceicon_account_status_otp_login'] = "mini_default_option.png"; }

//j// EOF
?>