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

use tool_policy\api;

defined('MOODLE_INTERNAL') || die();

/**
 * Utils
 *
 * @package   tool_gdpr_plus
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {
    /**
     * The key for storing the policies in the session.
     */
    const SESSION_KEY_POLICIES = 'gprd_plus_policies';
    /**
     * The key to store global acceptance status.
     */
    const SESSION_KEY_POLICIES_ACCEPTED = 'gprd_plus_policies_accepted';

    /**
     * Helper to determine the current is logged in but not as guest.
     *
     * @return bool
     */
    public static function is_loggedin_no_guest(): bool {
        return isloggedin() && !isguestuser();
    }

    /**
     * Return a filtered set of existing policies id
     *
     * @param array $policies associative array with two items : policyid and other info
     * @return array
     */
    public static function get_only_existing_policies(array $policies): array {
        return array_filter($policies, function($policy) {
            global $DB;
            return $DB->record_exists('tool_policy_versions', ['id' => $policy['policyid']]);
        });
    }

    /**
     * Set policy acceptance from an array of definition
     *
     * Note this will store information in the current session if user not yet logged in.
     *
     * @param array $policies associative array with two items : policyid and accepted
     */
    public static function set_policies_acceptances(array $policies): void {
        if (static::is_loggedin_no_guest()) {
            foreach ($policies as $policy) {
                $policiyid = $policy['policyid'];
                if ($policy['accepted']) {
                    api::accept_policies($policiyid);
                } else {
                    api::decline_policies($policiyid);
                }
            }
            api::update_policyagreed();
        } else {
            global $_SESSION;
            $_SESSION[self::SESSION_KEY_POLICIES] = $policies;
            $_SESSION[self::SESSION_KEY_POLICIES_ACCEPTED] = true;
        }
    }

    /**
     * Set policy acceptance from an array of definition
     *
     * Note this will store information in the current session if user not yet logged in.
     *
     * @param array $policies
     * @return array
     */
    public static function retrieve_policies_with_acceptance(array $policies): array {
        global $USER;
        if (empty($policies)) {
            return [];
        }
        if (static::is_loggedin_no_guest()) {
            $acceptances = api::get_user_acceptances($USER->id);
            foreach ($policies as $policy) {
                if (!empty($acceptances[$policy->id])) {
                    $policy->policyagreed = $acceptances[$policy->id]->status == "1";
                }
                $policy->mandatory = !($policy->optional == "1");
                if ($policy->mandatory) {
                    $policy->policyagreed = 1;
                }
            }
        } else {
            global $_SESSION;
            $sessionpolicies = $_SESSION[self::SESSION_KEY_POLICIES] ?? [];
            foreach ($policies as $policy) {
                $policy->policyagreed = false;
                foreach ($sessionpolicies as $localpolicies) {
                    if ($localpolicies['policyid'] == $policy->id) {
                        $policy->policyagreed = $localpolicies['accepted'];
                    }
                }
                $policy->mandatory = !($policy->optional == "1");
                if ($policy->mandatory) {
                    $policy->policyagreed = 1;
                }
            }
        }
        return $policies;
    }

    /**
     * Has policy been agreed
     *
     * @return bool
     */
    public static function has_policy_been_agreed(): bool {
        global $USER;
        if (static::is_loggedin_no_guest()) {
            return $USER->policyagreed;
        } else {
            global $_SESSION;
            return $_SESSION[self::SESSION_KEY_POLICIES_ACCEPTED] ?? false;
        }
    }
}
