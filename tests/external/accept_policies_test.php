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
        $acceptpolicies = accept_policies::execute(...$params);
        return external_api::clean_returnvalue(accept_policies::execute_returns(), $acceptpolicies);
    }

    /**
     * Test execute API CALL with no instance
     */
    public function test_execute_no_instance() {
        $acceptpolicies = $this->accept_policies([]);

        $this->assertIsArray($acceptpolicies);
        $this->assertArrayHasKey('warnings', $acceptpolicies);
    }

    /**
     * Test execute API CALL without login
     */
    public function test_execute_without_login() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $this->expectException(moodle_exception::class);
        $this->can_join($instance->get_cm_id());
    }

    /**
     * Test execute API CALL with invalid login
     */
    public function test_execute_with_invalid_login() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_user();
        $this->setUser($user);

        $this->expectException(moodle_exception::class);
        $this->can_join($instance->get_cm_id());
    }

    /**
     * When login as a student
     */
    public function test_execute_with_valid_login() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_and_enrol($course, 'student');
        $this->setUser($user);

        $canjoin = $this->can_join($instance->get_cm_id());

        $this->assertIsArray($canjoin);
        $this->assertArrayHasKey('can_join', $canjoin);
        $this->assertEquals(true, $canjoin['can_join']);
    }
}
