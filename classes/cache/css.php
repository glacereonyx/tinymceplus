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
 * CSS cache definition for editor_tinymceplus.
 * @package   editor_tinymceplus
 * @author    Ben Mitchell
 * @copyright (c) 2022 Ben Mitchell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace editor_tinymceplus\cache;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/csslib.php');

use cache;
use cache_data_source;
use cache_definition;
use coding_exception;
use core_scss;
use moodle_exception;

/**
 * CSS cache definition for editor_tinymceplus.
 */
class css implements cache_data_source {

    /** @var css */
    protected static $instance = null;

    /**
     * Returns an instance of the data source class that the cache can use for loading data using the other methods
     * specified by this interface.
     *
     * @param cache_definition $definition
     * @return object
     */
    public static function get_instance_for_cache(cache_definition $definition): css {
        if (is_null(self::$instance)) {
            self::$instance = new css();
        }
        return self::$instance;
    }

    /**
     * Returns the processed CSS.
     * @return string
     */
    public function get_css() {
        return $this->get_data_cache()->get('css');
    }

    /**
     * Deletes the cached CSS so that it will be rebuilt from sass on next cache load.
     * @return void
     */
    public function flush_css() {
        return $this->get_data_cache()->delete('css');
    }

    /**
     * Get an instance of this data cache.
     * @return cache_application|cache_session|cache_store the blockconfig cache we are using.
     */
    protected function get_data_cache() {
        // Do not double cache here because it may break cache resetting.
        return cache::make('editor_tinymceplus', 'css');
    }

    /**
     * Loads the data for the key provided ready formatted for caching.
     *
     * @param string|int $key The key to load.
     * @return mixed What ever data should be returned, or false if it can't be loaded.
     */
    public function load_for_cache($key = 'css') {
        if ($key !== 'css') {
            throw new coding_exception('invalid cache key. only css is valid');
        }
        return $this->generate_css();
    }

    /**
     * Generates the CSS for the editor using sass and the colour values passed in via the admin config.
     * @return string
     */
    private function generate_css() {
        global $CFG;

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
        return $css;
    }

    /**
     * NOT USED. This cache does not store more than one object.
     * @param array $keys NOT USED.
     */
    public function load_many_for_cache(array $keys) {
        throw new coding_exception('Cache does not support loading multiple keys.');
    }
}
