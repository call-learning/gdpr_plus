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

namespace tool_gdpr_plus\external;

use external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for the update_course class.
 *
 * @package   tool_gdpr_plus
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accept_policies_test extends \externallib_advanced_testcase {

    /**
     * Setup test
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->resetAfterTest();
        set_config('sitepolicyhandler', 'tool_gdpr_plus');
    }

    /**
     * Helper
     *
     * @param ... $params
     * @return mixed
     */
    protected function accept_policies(...$params) {
        $acceptpolicies = accept_policies::execute(... $params);
        return external_api::clean_returnvalue(accept_policies::execute_returns(), $acceptpolicies);
    }

    /***
     * Accept no policies
     *
     * @return void
     */
    public function test_accept_nopolicies() {
        $result = $this->accept_policies([]);
        $this->assertNotEmpty($result);
        $this->assertEmpty($result['warnings']);
    }

    /**
     * Accept wrong parameters
     *
     * @return void
     */
    public function test_accept_wrong_parameters() {
        $this->expectException('invalid_parameter_exception');
        $this->accept_policies(['policies' => ['policyid' => 9999]]);
    }

    /**
     * Accept non existing policy
     *
     * @return void
     */
    public function test_accept_non_existing() {
        $result = $this->accept_policies(['policies' => ['policyid' => 9999, 'accepted' => true]]);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals([
            'item' => 'policyid',
            'itemid' => 9999,
            'warningcode' => 'invalidpolicyid',
            'message' => 'Invalid policy id',
        ], $result['warnings'][0]);
    }

    /**
     * Accept non existing policy
     *
     * @return void
     */
    public function test_accept_non_logged_in() {
        $result = $this->accept_policies(['policies' => ['policyid' => 9999, 'accepted' => true]]);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals([
            'item' => 'policyid',
            'itemid' => 9999,
            'warningcode' => 'invalidpolicyid',
            'message' => 'Invalid policy id',
        ], $result['warnings'][0]);
    }
}
