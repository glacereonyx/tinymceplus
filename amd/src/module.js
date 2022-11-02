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

/** A list of ExecCommands that should be ignored when triggering the input event on the original textarea */
const inputUpdateBlacklist = [
  'mceFocus', 'mceWordCount', 'mceMedia', 'mceLink', 'mceCodeEditor', 'SelectAll'
];

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

  if (options.enable_filemanagement == true) {
    if (foptions !== null) {
      // TODO: Implement the image upload handler.
      // options.images_upload_handler = image_upload_handler;

      options.file_picker_callback = file_picker_callback;
      options.file_picker_types = 'image'; // TODO: add media and file
    } else {
      console.warn('enable_filemanagement is true, however no "fileoptions" have been provided to the texteditor.');
    }
  }

  // Run extra setup on the editor instance
  options.setup = (editor) => {
    editor.fileOptions = foptions;

    editor.on('input', function (e) {
      sync_textarea(editor);
    });
    editor.on('ExecCommand', function (e) {
      if (inputUpdateBlacklist.indexOf(e.command) != -1) { // Return early if this is a blacklisted command.
        return;
      }
      console.log(`The ${e.command} command was fired.`);
      sync_textarea(editor);
    });

    const target = editor.getElement();

    editor.on('SetContent', function(e) {
      if (target.textContent != editor.getContent()){
        sync_textarea(editor);
      }
    });

    // Listen for readonly changes on the initial textarea.
    target.addEventListener('form:editorUpdated', function() {
      if (target.readOnly) {
        editor.mode.set('readonly');
        return;
      }
      editor.mode.set('design');
  });
  };
  options.hidden_input = false;

  tinymce.init(options);

};

/**
 * Syncs the attatched textarea with the content of TinyMCE. Also sends an input event from the textarea.
 * @param {object} editor A reference to the editor object.
 */
function sync_textarea(editor) {
  if (!editor) {
    return;
  }
 const target = editor.getElement();
 target.textContent = editor.getContent();
 target.dispatchEvent(new Event('input', {bubbles:true}));
}

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
  const fileOptions = tinymce.activeEditor.fileOptions;

  YUI().use('core_filepicker', function (Y) { // Using repository/filepicker.js

    let options = null;

    if (meta.filetype == 'image') {
      options = fileOptions.image;
    } else if (meta.filetype == 'file') {
      options = fileOptions.file;
    } else if (meta.filetype == 'media') {
      options = fileOptions.media;
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