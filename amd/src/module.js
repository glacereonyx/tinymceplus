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
 * Functions used to initialize the TinyMCE Plus editor
 *
 * @copyright 2022 Ben Mitchell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const tinymce = window.tinymce;
const YUI = window.YUI;

/**
 * Initializes a TinyMCE editor instance.
 * @param {Array} options
 * @param {Array} foptions
 */
export const init_editor = (options, foptions) => {

  // Set the min height to be the height of the texeditor.
  const initheight = document.querySelector(options.selector)?.offsetHeight;
  options.min_height = initheight || 200;
  options.min_height += 25;

  // Initialize the editor at the minheight.
  options.height = options.min_height;

  // Run extra setup on the editor instance
  options.setup = (editor) => {
    editor.fileOptions = foptions;
  };

  // TODO: Implement the image upload handler.
  // options.images_upload_handler = image_upload_handler;

  options.file_picker_callback = file_picker_callback;
  options.file_picker_types = 'image'; // TODO: add media and file

  tinymce.init(options);

};

/**
 * Uses Moodle's AJAX plugin to upload images using a web service.
 * @param {string} blobInfo
 * @param {number} progress
 * @returns {Promise}
 */
const image_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
  // TODO: Write an implementation to work around Moodle's filepicker.js popup.
});

/**
 * Integrates the Moodle's repository/filepicker.js with TinyMCE
 * @param {requestCallback} callback
 * @param {string} value
 * @param {object} meta
 */
const file_picker_callback = (callback, value, meta) => {
  console.log(typeof value, typeof meta);
  const fileOptions = tinymce.activeEditor.fileOptions;

  YUI().use('core_filepicker', function (Y) { // Using repository/filepicker.js

    let options = null;

    if (meta.filetype == 'image') {
      options = fileOptions['image'];
    }

    options.formcallback = (fileInfo) => {
      console.log(fileInfo);
      callback(fileInfo.url);
    };

    // TODO: See if we need this.
    // options.editor_target = win.document.getElementById(target_id);

    M.core_filepicker.show(Y, options);

  });
};