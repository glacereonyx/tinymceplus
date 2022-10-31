/* eslint no-unused-vars: 0
          no-console: 0 */

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
 * Helper function to inject custom dynamic CSS from the plugin into the page.
 *
 * @copyright 2022 Ben Mitchell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Injects the editor styles.
 * @param {string} css The CSS to inject.
 */
export const cssinject = (css) => {

  // Hack to prevent multiple injections.
  if (window?.editor_tinymceplus?.injected) {
    return;
  }

  if (!window.editor_tinymceplus) {
    window.editor_tinymceplus = {};
  }

  const style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  window.editor_tinymceplus.injected = true;
};