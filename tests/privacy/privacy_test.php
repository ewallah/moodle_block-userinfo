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
 * @copyright  2011 Federico J. Botti - Entornos Educativos
 * @author     Renaat Debleu <info@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_userinfo\privacy;

use core_privacy\tests\provider_testcase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for block_userinfo.
 *
 * @package    block_userinfo
 * @category   test
 * @copyright  2011 Federico J. Botti - Entornos Educativos
 * @author     Renaat Debleu <info@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(provider::class)]
final class privacy_test extends provider_testcase {
    /**
     * Test privacy.
     */
    public function test_privacy(): void {
        $privacy = new provider();
        $this->assertEquals('privacy:metadata', $privacy->get_reason());
    }
}
