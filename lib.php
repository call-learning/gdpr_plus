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
 * Theme plugin version settings.
 *
 * @package   local_gprd_plus
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Load policy message for guests.
 *
 * @return string The HTML code to insert before the head.
 */
function local_gprd_plus_before_footer() {
    global $CFG, $PAGE, $USER;

    $message = null;
    if (!empty($CFG->sitepolicyhandler)
        && $CFG->sitepolicyhandler == 'local_gprd_plus'
        && empty($USER->policyagreed)
        && (isguestuser() || !isloggedin())) {
        $output = $PAGE->get_renderer('local_gprd_plus');
        try {
            $page = new \local_gprd_plus\output\guestconsent();
            $message = $output->render($page);
        } catch (dml_read_exception $e) {
            // During upgrades, the new plugin code with new SQL could be in place but the DB not upgraded yet.
            $message = null;
        }
    }

    echo $message;
}
