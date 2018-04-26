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
 * Block userinfo class
 *
 * @package    block_userinfo
 * @copyright  2011 Federico J. Botti - Entornos Educativos
 * @author     2018 Renaat Debleu <rdebleu@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Block userinfo class
 *
 * @package    block_userinfo
 * @copyright  2011 Federico J. Botti - Entornos Educativos
 * @author     2018 Renaat Debleu <rdebleu@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_userinfo extends block_base {

    /**
     * Initialise the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_userinfo');
    }

    /**
     * Return the content of this block.
     *
     * @return stdClass the content
     */
    public function get_content() {
        global $CFG, $OUTPUT, $USER, $DB;

        require_once($CFG->dirroot . '/message/lib.php');

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        if ($USER->id == 1) {
            return $this->content;
        }
        $s = '';
        if (isloggedin()) {
            $this->title = $this->salute();
            $s = '<br /><span></span>';
            $s .= $OUTPUT->user_picture($USER, ['size' => 100, 'class' => 'userinfoblockimg']);
            $s .= '<br/>';
            $s .= html_writer::link(new moodle_url('/user/profile.php', ['id' => $USER->id]), fullname($USER, true));
            $s .= '&nbsp;(';
            $s .= html_writer::link(new moodle_url('/login/logout.php', ['sesskey' => sesskey()]), get_string('logout'));
            $s .= ')';
            $s = html_writer::div($s, 'userinfoblock');
            $s .= '<br />';
            if ($USER->firstname != 'guest') {
                $txt = get_string('editmyprofile', 'block_userinfo');
                $img = $OUTPUT->pix_icon('i/edit', $txt);
                $s .= html_writer::link(new moodle_url('/user/edit.php', ['id' => $USER->id]), $img . '&nbsp;' . $txt);
                $s .= '<br />';
                $txt = get_string('messages', 'block_userinfo');
                $img = $OUTPUT->pix_icon('t/email', $txt);
                $txt .= '&nbsp;(' . message_count_unread_messages($USER) . ')';
                $s .= html_writer::link(new moodle_url('/message/index.php'), $img . '&nbsp;' . $txt);
                $s .= '<br />';
                $txt = get_string('mycourses', 'block_userinfo');
                $img = $OUTPUT->pix_icon('i/course', $txt);
                $s .= html_writer::link(new moodle_url('/my/'), $img . '&nbsp;' . $txt);
                $s .= '<br />';
                if ($USER->picture == 0) {
                    if (!$DB->get_field('user', 'description', array('id' => $USER->id))) {
                        $txt = get_string('incomplete', 'mod_scorm');
                        $s .= $OUTPUT->pix_icon('i/risk_xss', $txt) . '&nbsp;' . $txt . '<br />';
                    }
                }
            }
            $txt = get_string('lastaccess') . ': ' . userdate($USER->lastlogin, get_string('strftimedatetime', 'core_langconfig'));
            $s .= html_writer::span($txt, 'lastaccess');
        }
        $this->content->text = $s;
        return $this->content;
    }

    /**
     * Return the salute.
     *
     * @return string
     */
    private function salute() {
        $date = new DateTime('now', new DateTimeZone(core_date::normalise_timezone(99)));
        $tmz = $date->getOffset() - dst_offset_on(time(), 99);
        if ($tmz == 99) {
            $ut = (date('G') * 3600 + date('i') * 60 + date('s')) / 3600;
        } else {
            $tz = core_date::get_user_timezone(99);
            $date = new DateTime('now', new DateTimeZone($tz));
            $loc = ($date->getOffset() - dst_offset_on(time(), $tz)) / (3600.0);
            $ut = ((gmdate('G') + $loc) * 3600 + gmdate('i') * 60 + gmdate('s')) / 3600;
            if ($ut <= 0) {
                $ut = 24 + $ut;
            }
            if ($ut > 24) {
                $ut = $ut - 24;
            }
        }
        if ($ut < 12) {
            return get_string('morning', 'block_userinfo');
        }
        if (($ut >= 12) and ($ut < 19)) {
            return get_string('afternoon', 'block_userinfo');
        }
        return get_string('night', 'block_userinfo');
    }
}