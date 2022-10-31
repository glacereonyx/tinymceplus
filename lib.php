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
 * Library file for editor_tinymceplus
 *
 * @package   editor_tinymceplus
 * @author    Ben Mitchell
 * @copyright (c) 2022 Ben Mitchell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/csslib.php');

/**
 * Configuration for the tinymceplus texteditor
 */
class tinymceplus_texteditor extends texteditor {
    /** @var string active version. Used as the directory name to get the tinymce code. */
    protected $version = '6.2.0';

    /**
     * Gets the active version. Used as the directory name to get the tinymce code.
     * @return string tinymce version.
     */
    public function get_version() : string {
        return $this->version;
    }

    /**
     * Gets the installed location of the current tinyMCE version.
     * @param string $dir Do not include leading slash.
     *                    An optional extension to the url.
     *                    Usually used to get a specific file in the tinymce directory.
     * @return moodle_url
     */
    public function get_tinymceplus_base_url(string $dir = '') : moodle_url {
        return new moodle_url("/lib/editor/tinymceplus/tiny_mce/{$this->get_version()}/{$dir}");
    }

    /**
     * Is the current browser supported by this editor?
     * @return bool
     */
    public function supported_by_browser() {
        // We don't support any browsers which it doesn't support.
        return true;
    }

    /**
     * Returns array of supported text formats.
     * @return array
     */
    public function get_supported_formats() {
        // FORMAT_MOODLE is not supported here, sorry.
        return array(FORMAT_HTML => FORMAT_HTML);
    }

    /**
     * Returns text format preferred by this editor.
     * @return int
     */
    public function get_preferred_format() {
        return FORMAT_HTML;
    }

    /**
     * Does this editor support picking from repositories?
     * @return bool
     */
    public function supports_repositories() {
        return false; // TODO: Implement repository support.
    }

    /**
     * Use this editor for given element.
     * @param string $elementid
     * @param array $options
     * @param array $foptions
     */
    public function use_editor($elementid, ?array $options = null, $foptions = null) {
        global $CFG, $PAGE;

        $PAGE->requires->js($this->get_tinymceplus_base_url('tinymce.min.js'));

        $PAGE->requires->js_call_amd('editor_tinymceplus/module', 'init_editor', [$this->get_init_params($elementid), $foptions]);

        $compiler = new core_scss();
        $compiler->set_file($CFG->dirroot . '/lib/editor/tinymceplus/styles.scss');
        $compiler->setVariables([
            'toolbar-btn-hover' => get_config('editor_tinymceplus', 'theme_toolbar_btn_hover'),
            'primary-btn' => get_config('editor_tinymceplus', 'theme_primary_btn'),
            'primary-btn-hover' => get_config('editor_tinymceplus', 'theme_primary_btn_hover'),
            'primary-btn-text' => get_config('editor_tinymceplus', 'theme_primary_btn_text'),
        ]);
        $css = '';
        try {
            $css = $compiler->to_css();
        } catch (\Exception $e) {
            debugging('Error while compiling editor SCSS: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }

        $PAGE->requires->js_call_amd('editor_tinymceplus/cssbarge', 'cssinject', [$css]);

    }

    /**
     * Generates editor init params for a specific editor element.
     * @param string $elementid
     */
    public function get_init_params($elementid) {
        global $CFG, $PAGE;

        $directionality = get_string('thisdirection', 'langconfig');
        $strtime        = get_string('strftimetime');
        $strdateabbr   = get_string('strftimedatemonthabbr', 'langconfig');
        $strdate        = get_string('strftimedaydate');
        $lang = current_language();

        $config = get_config('editor_tinymceplus');

        $params = [
            'selector' => 'textarea#' . $elementid,
            'promotion' => false,
            'branding' => ($config->showbranding == 1) ? true : false,
            'menubar' => false,
            'contextmenu' => false,
            'relative_urls' => false,
            'remove_script_host' => false,
            'document_base_url' => $CFG->wwwroot,
            'language' => $lang,
            'directionality' => $directionality,

            // Remove options that should be controlled by Moodle theme.
            'block_formats' => 'Heading (large)=h3; Heading (medium)=h4; Heading (small)=h5; Preformatted=pre; Paragraph=p;',
            'custom_colors' => false,
            'font_family_formats' => '',
            'font_size_formats' => '',
            'line_height_formats' => '',
            'removed_menuitems' => 'newdocument print',

            // Toolbar & Plugin config.
            'toolbar' => [''], // Do not set values here. They will be overriden by parse_toolbar_setting.
            'plugins' => ['code', 'directionality', 'image', 'insertdatetime', 'link', 'lists',
                    'media', 'quickbars', 'searchreplace', 'table', 'visualblocks', 'visualchars', 'wordcount'],
            'insertdatetime_dateformat' => $strdate,
            'insertdatetime_timeformat' => $strtime,
            'insertdatetime_formats' => [$strtime, $strdateabbr, $strdate],
            'quickbars_insert_toolbar' => false,
            'quickbars_image_toolbar' => false,
            'quickbars_selection_toolbar' => 'underline bold italic | bullist numlist | outdent indent',
            'media_poster' => false,
        ];

        if ($config->useeditorcss == 1) {
            $contentcss = $PAGE->theme->editor_css_url()->out(false);
            $params ['content_css'] = $contentcss;
        }

        // Set the customtoolbar based on config.
        if (!empty($config->customtoolbar) && $customtoolbar = self::parse_toolbar_setting($config->customtoolbar)) {
            $params['toolbar'] = $customtoolbar;
        }

        return $params;
    }

    /**
     * Convert the toolbar config string into something usable by TinyMCE
     * @param string $customtoolbar
     */
    public static function parse_toolbar_setting($customtoolbar) : array {
        $result = [];

        $customtoolbar = trim($customtoolbar);
        if ($customtoolbar === '') {
            return $result;
        }

        $customtoolbar = str_replace("\r", "\n", $customtoolbar);
        $customtoolbar = strtolower($customtoolbar);

        $i = 0;
        foreach (explode("\n", $customtoolbar) as $line) {

            // Replace all characters that arent alphanumeric, underscore, pipe, hyphen or space with a space.
            $line = preg_replace('/[^a-z0-9_ \|\-]/', ' ', $line);
            $line = str_replace('|', ' | ', $line); // Make sure all pipes have a space around them.
            $line = preg_replace('/  +/', ' ', $line); // Replace double spaces with a single space.
            $line = trim($line, ' |'); // Trim extra spaces and pipes from line.

            if ($line === '') {
                continue;
            }
            if ($i == 10) {
                // Maximum is ten lines, merge the rest to the last line.
                $result[9] = $result[9].' '.$line;
            } else {
                $result[] = $line;
                $i++;
            }
        }
        return $result;
    }



}
