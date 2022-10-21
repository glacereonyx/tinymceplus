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
 * @package   editor_tinymceplus
 * @author    Ben Mitchell
 * @copyright (c) 2022 Ben Mitchell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * Configuration for the tinymceplus texteditor
  */
class tinymceplus_texteditor extends texteditor {
    /** @var string active version. Used as the directory name to get the tinymce code. */
    protected string $version = '6.2.0';

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
     */
    public function use_editor($elementid, ?array $options = null, $fpoptions = null) {
        global $PAGE;

        $PAGE->requires->js($this->get_tinymceplus_base_url('tinymce.min.js'));

        $PAGE->requires->js_call_amd('editor_tinymceplus/module', 'init_editor', [$this->get_init_params($elementid)]);
    }

    public function get_init_params($elementid) {
        $params = [
            'selector' => 'textarea#' . $elementid,
            'promotion' => false,
            'menubar' => false,
            'plugins' => ['code', 'link'],
        ];
        return $params;
    }



}
