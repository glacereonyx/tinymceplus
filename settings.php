<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Configures the admin settings for TinyMCEPlus.
 *
 * @package   editor_tinymceplus
 * @author    Ben Mitchell
 * @copyright (c) 2022 Ben Mitchell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('editorsettings', new admin_category('editortinymceplus', $editor->displayname, $editor->is_enabled() === false));

$settings = new admin_settingpage('editorsettingstinymceplus', new lang_string('settings', 'editor_tinymceplus'));
if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('tinymcegeneralheader', new lang_string('settings'), ''));
    $default = 'undo redo | blocks | underline bold italic | alignleft aligncenter alignright alignjustify | '
              .'bullist numlist | outdent indent | table | link code | searchreplace wordcount';

    // Toolbar settings.
    $settings->add(
      new admin_setting_configtextarea('editor_tinymceplus/customtoolbar',
      get_string('customtoolbar', 'editor_tinymceplus'),
      get_string('customtoolbar_desc', 'editor_tinymceplus',
      'https://www.tiny.cloud/docs/tinymce/6/available-toolbar-buttons/'),
      $default, PARAM_RAW, 100, 8
    ));

    // Show TinyMCE branding.
    $settings->add(
      new admin_setting_configcheckbox('editor_tinymceplus/showbranding',
        get_string('showbranding', 'editor_tinymceplus'),
        get_string('showbranding_desc', 'editor_tinymceplus',
          'https://www.tiny.cloud/legal/attribution-requirements/'),
        1
        ));

    // Use editorCSS.
    $settings->add(
      new admin_setting_configcheckbox('editor_tinymceplus/useeditorcss',
      get_string('useeditorcss', 'editor_tinymceplus'),
      get_string('useeditorcss_desc', 'editor_tinymceplus'),
      0
    ));

}

$ADMIN->add('editortinymceplus', $settings);

unset($settings);
$settings = null;
