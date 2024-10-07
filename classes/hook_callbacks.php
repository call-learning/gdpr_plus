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
namespace tool_gdpr_plus;

use core\hook\output\before_footer_html_generation;
use dml_read_exception;
use html_writer;
use moodle_url;
use tool_policy\api;

/**
 * Hook callbacks for tool_gdpr_plus.
 *
 * @package    tool_usertours
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Load policy message for guests.
     * @param before_footer_html_generation $hook
     *
     * @return string The HTML code to insert before the head.
     *
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $CFG, $PAGE;

        $message = null;
        if (!empty($CFG->sitepolicyhandler)
            && $CFG->sitepolicyhandler == 'tool_gdpr_plus') {
            $output = $PAGE->get_renderer('tool_gdpr_plus');
            try {
                $page = new \tool_gdpr_plus\output\policies_consent();
                $message = $output->render($page);
                $policies = api::get_current_versions_ids();
                if (!empty($policies)) {
                    $url = new moodle_url('/admin/tool/policy/viewall.php', ['returnurl' => $PAGE->url]);
                    $output = html_writer::link($url, get_string('userpolicysettings', 'tool_policy'));
                    $message .= html_writer::div($output, 'policiesfooter');
                }
            } catch (dml_read_exception $e) {
                // During upgrades, the new plugin code with new SQL could be in place but the DB not upgraded yet.
                $message = null;
            }
        }
        $hook->add_html($message);
    }
}
