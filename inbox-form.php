<?php

/**
 * Plugin Name: INBOX Forms
 * Description: INBOX Forms plugin for WordPress.
 * Version: 1.0.4
 * Author: INBOX
 * Author URI: https://useinbox.com/
 * License: GPLv2 or later
 * Text Domain: inbox
 * Domain Path: /languages
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
 * USA.
 */

require __DIR__ . '/vendor/autoload.php';

define('INBOX_SYSTEM_FILE_PATH', __FILE__);
define('INBOX_VERSION_NUMBER', '1.0.4');

add_action('init', 'ib_inbox_load_plugin_textdomain', 0);
function ib_inbox_load_plugin_textdomain()
{
    load_plugin_textdomain('inbox', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

Inbox\Core::init();
