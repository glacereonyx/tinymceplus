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

    $settings->add(new admin_setting_heading('tinymceplusgeneralheader', new lang_string('settings'), ''));

    $default = 'undo redo | blocks | underline bold italic | alignleft aligncenter alignright alignjustify | '
              .'bullist numlist | outdent indent | table | image file media | link code | searchreplace wordcount';

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

    // Theming options.
    $settings->add(new admin_setting_heading('tinymceplusthemeheader', new lang_string('theme'), ''));

    $range = range(0, 20);
    array_walk($range, fn(&$value, $key) => $value .= 'px');
    $options = array_combine($range, $range);
    $setting = new admin_setting_configselect('editor_tinymceplus/theme_editor_border_radius',
    get_string('theme:editor_border_radius', 'editor_tinymceplus'),
    get_string('theme:editor_border_radius_desc', 'editor_tinymceplus'),
    '5px', $options);
    $setting->set_updatedcallback('editor_tinymceplus_reset_css_cache');
    $settings->add($setting);

    $setting = new admin_setting_configcolourpicker('editor_tinymceplus/theme_toolbar_btn_hover',
    get_string('theme:toolbar_btn_hover', 'editor_tinymceplus'),
    get_string('theme:toolbar_btn_hover_desc', 'editor_tinymceplus'),
    '#cce2fa'
    );
    $setting->set_updatedcallback('editor_tinymceplus_reset_css_cache');
    $settings->add($setting);

    $setting = new admin_setting_configcolourpicker('editor_tinymceplus/theme_primary_btn',
    get_string('theme:primary_btn', 'editor_tinymceplus'),
    get_string('theme:primary_btn_desc', 'editor_tinymceplus'),
    '#0054b4'
    );
    $setting->set_updatedcallback('editor_tinymceplus_reset_css_cache');
    $settings->add($setting);

    $setting = new admin_setting_configcolourpicker('editor_tinymceplus/theme_primary_btn_hover',
    get_string('theme:primary_btn_hover', 'editor_tinymceplus'),
    get_string('theme:primary_btn_hover_desc', 'editor_tinymceplus'),
    '#0060ce'
    );
    $setting->set_updatedcallback('editor_tinymceplus_reset_css_cache');
    $settings->add($setting);

    $setting = new admin_setting_configcolourpicker('editor_tinymceplus/theme_primary_btn_text',
    get_string('theme:primary_btn_text', 'editor_tinymceplus'),
    get_string('theme:primary_btn_text_desc', 'editor_tinymceplus'),
    '#fff'
    );
    $setting->set_updatedcallback('editor_tinymceplus_reset_css_cache');
    $settings->add($setting);

}

$ADMIN->add('editortinymceplus', $settings);

unset($settings);
$settings = null;
