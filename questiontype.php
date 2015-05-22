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
 * Question type class for the fileresponse question type.
 *
 * @package    qtype
 * @subpackage fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');


/**
 * The fileresponse question type.
 *
 * @copyright  2005 Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_fileresponse extends question_type {
    public function is_manual_graded() {
        return true;
    }

    public function response_file_areas() {
        return array('attachments', 'answer');
    }

    public function get_question_options($question) {
        global $DB;
        $question->options = $DB->get_record('qtype_fileresponse_options',
                array('questionid' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }

    public function save_question_options($formdata) {
        global $DB;
        $context = $formdata->context;

        $options = $DB->get_record('qtype_fileresponse_options', array('questionid' => $formdata->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $formdata->id;
            $options->id = $DB->insert_record('qtype_fileresponse_options', $options);
        }

        /* fileresponse only accepts 'formatplain' as format */
        $options->responseformat = 'plain';
        $options->responsefieldlines = $formdata->responsefieldlines;
        $options->attachments = $formdata->attachments;
        $options->forcedownload = $formdata->forcedownload;
        $options->allowpickerplugins = $formdata->allowpickerplugins;
        $options->graderinfo = $this->import_or_save_files($formdata->graderinfo,
                $context, 'qtype_fileresponse', 'graderinfo', $formdata->id);
        $options->graderinfoformat = $formdata->graderinfo['format'];
        /* fileresponse doesn't display a response template */
        $options->responsetemplate = '';
        /* fileresponse doesn't display a response template */
        $options->responsetemplateformat = FORMAT_HTML;
        $DB->update_record('qtype_fileresponse_options', $options);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        /* fileresponse only accepts 'formatplain' as format */
        $question->responseformat = 'plain';
        $question->responsefieldlines = $questiondata->options->responsefieldlines;
        $question->attachments = $questiondata->options->attachments;
        $question->forcedownload = $questiondata->options->forcedownload;
        $question->allowpickerplugins = $questiondata->options->allowpickerplugins;
        $question->graderinfo = $questiondata->options->graderinfo;
        $question->graderinfoformat = $questiondata->options->graderinfoformat;
        /* fileresponse doesn't display a response template */
        $question->responsetemplate = '';
        /* fileresponse doesn't display a response template */
        $question->responsetemplateformat = 'plain';
    }

    public function delete_question($questionid, $contextid) {
        global $DB;

        $DB->delete_records('qtype_fileresponse_options', array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }

    /**
     * @return array the different response formats that the question type supports.
     * internal name => human-readable name.
     */
    public function response_formats() {
        /* fileresponse only accepts 'formatplain' as format */
        return array(
            'plain' => get_string('formatplain', 'qtype_fileresponse')
        );
    }

    /**
     * @return array the choices that should be offered for the input box size.
     */
    public function response_sizes() {
        $choices = array();
        for ($lines = 0; $lines <= 40; $lines += 5) {
            if ($lines == 0) {
                $choices[$lines] = get_string('noinputbox', 'qtype_fileresponse');
            } else {
                $choices[$lines] = get_string('nlines', 'qtype_fileresponse', $lines);
            }
        }
        return $choices;
    }

    /**
     * @return array the choices that should be offered for the number of attachments.
     */
    public function attachment_options() {
        return array(
        /* fileresponse has to have at least one file required */
            // 0 => get_string('no'),
            1 => '1',
            2 => '2',
            3 => '3',
            -1 => get_string('unlimited'),
        );
    }

    /**
     * @return array the choices that should be offered for the forcedownload.
     */
    public function forcedownload_options() {
        return array(
            0 => get_string('withdownload', 'qtype_fileresponse'),
            1 => get_string('withoutdownload', 'qtype_fileresponse'),

        );
    }

    /**
     * @return array the choices that should be offered for the allowpickerplugins.
     */
    public function allowpickerplugins_options() {
        return array(
            0 => get_string('allowpickerpluginsno', 'qtype_fileresponse'),
            1 => get_string('allowpickerpluginsyes', 'qtype_fileresponse'),

        );
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $fs = get_file_storage();
        $fs->move_area_files_to_new_context($oldcontextid,
                $newcontextid, 'qtype_fileresponse', 'graderinfo', $questionid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $fs = get_file_storage();
        $fs->delete_area_files($contextid, 'qtype_fileresponse', 'graderinfo', $questionid);
    }
}
