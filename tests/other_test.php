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
 * Tests for block_userinfo.
 *
 * @package    block_userinfo
 * @category   test
 * @copyright  2018 Renaat Debleu <rdebleu@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_userinfo;

use stdClass;

/**
 * Unit tests for block_userinfo/classes/privacy/policy
 *
 * @package    block_userinfo
 * @category   test
 * @copyright  2018 Renaat Debleu <rdebleu@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class other_test extends \advanced_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp():void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
    }

    /**
     * Test privacy.
     */
    public function test_privacy() {
        $privacy = new privacy\provider();
        $this->assertEquals('privacy:metadata', $privacy->get_reason());
    }

    /**
     * Test basic block.
     */
    public function test_block_basic() {
        global $DB, $USER;
        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $ctx = \context_course::instance($course->id);
        $block = self::create_block($ctx);
        $this->assertFalse($block->instance_allow_multiple());
        $this->assertNotEmpty($block->applicable_formats());
        $this->assertStringContainsString('Edit my profile', $block->get_content()->text);
        $this->assertStringContainsString('Last access:', $block->get_content()->text);

        $this->setGuestUser();
        $block = self::create_block($ctx);
        $this->assertEquals('', $block->get_content()->text);

        $course = $dg->create_course(['lang' => 'fr']);
        $ctx = \context_course::instance($course->id);
        $user = $dg->create_user();
        // MDL-68333 hack when nl language is not installed.
        $DB->set_field('user', 'lang', 'nl', ['id' => $user->id]);
        $dg->enrol_user($USER->id, $course->id);
        $this->setUser($user->id);
        $block = self::create_block($ctx);
        // The string is not translated because the language pack is not installed.
        $this->assertStringContainsString('Edit my profile', $block->get_content()->text);
        $this->assertNotEmpty($block->salute());
    }

    /**
     * Test create block.
     * @param stdClass $ctx Context
     * @return stdClass block
     */
    private static function create_block($ctx) {
        $page = new \moodle_page();
        $page->set_context($ctx);
        $page->set_pagetype('region-a');
        $page->set_subpage('');
        $page->set_url(new \moodle_url('/'));
        $blockmanager = new \block_manager($page);
        $blockmanager->add_regions(['region-a'], false);
        $blockmanager->set_default_region('region-a');
        $blockmanager->add_block('userinfo', 'region-a', -10, false);
        $blockmanager->load_blocks();
        $blocks = $blockmanager->get_blocks_for_region('region-a');
        return $blocks[0];
    }
}
