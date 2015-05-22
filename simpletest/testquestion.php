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
 * Unit tests for the fileresponse question definition class.
 *
 * @package    qtype
 * @subpackage fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/simpletest/helpers.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_fileresponse_question_test extends UnitTestCase {
    public function test_get_question_summary() {
        $fileresponse = self::make_an_fileresponse_question();
        $fileresponse->questiontext = 'Hello <img src="http://example.com/globe.png" alt="world" />';
        $this->assertEqual('Hello [world]', $fileresponse->get_question_summary());
    }

    public function test_summarise_response() {
        $longstring = str_repeat('0123456789', 50);
        $fileresponse = self::make_an_fileresponse_question();
        $this->assertEqual($longstring,
                $fileresponse->summarise_response(array('answer' => $longstring)));
    }

    /**
     * Makes a fileresponse question, defaultmark 1.
     * @return qtype_fileresponse_question
     */
    public static function make_an_fileresponse_question() {
        question_bank::load_question_definition_classes('fileresponse');
        $fileresponse = new qtype_fileresponse_question();
        self::initialise_a_question($fileresponse);
        $fileresponse->name = 'Fileresponse question';
        $fileresponse->questiontext = 'Upload a file.';
        $fileresponse->generalfeedback = 'I hope you uploaded an interesting file.';
        $fileresponse->penalty = 0;
        $fileresponse->qtype = question_bank::get_qtype('fileresponse');

        $fileresponse->responsefieldlines = 5;
        $fileresponse->attachments = 0;
        $fileresponse->graderinfo = '';
        $fileresponse->graderinfoformat = FORMAT_MOODLE;

        return $fileresponse;
    }

     /**
     * Initialise the common fields of a question of any type.
     */
    public static function initialise_a_question($q) {
        global $USER;

        $q->id = 0;
        $q->category = 0;
        $q->parent = 0;
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->length = 1;
        $q->stamp = make_unique_id_code();
        $q->version = make_unique_id_code();
        $q->hidden = 0;
        $q->timecreated = time();
        $q->timemodified = time();
        $q->createdby = $USER->id;
        $q->modifiedby = $USER->id;
    }
}