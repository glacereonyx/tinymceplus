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

require(__DIR__ . '/../../../config.php');

require_login();

$PAGE->set_url(new moodle_url('/lib/editor/tinymceplus/test.php'));
$PAGE->set_context(context_system::instance());

$config = [
  'context' => $PAGE->context,
  'autosave' => false,
  'enable_filemanagement' => false,
];
$editor = editors_get_preferred_editor(1);
$editor->use_editor('test', $config);

echo $OUTPUT->header();
?>

<textarea name="" id="test" cols="90" rows="10"></textarea>

<?php
echo $OUTPUT->footer();
