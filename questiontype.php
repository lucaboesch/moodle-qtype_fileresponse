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
 * @package    qtype_fileresponse
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

        /* Fileresponse only accepts 'plain' as format. */
        $options->responseformat = 'plain';
        $options->responsefieldlines = $formdata->responsefieldlines;
        $options->attachments = $formdata->attachments;
        $options->forcedownload = $formdata->forcedownload;
        $options->allowpickerplugins = $formdata->allowpickerplugins;
        if (!isset($formdata->filetypeslist)) {
            $options->filetypeslist = "";
        } else {
            $options->filetypeslist = $formdata->filetypeslist;
        }
        $options->graderinfo = $this->import_or_save_files($formdata->graderinfo,
            $context, 'qtype_fileresponse', 'graderinfo', $formdata->id);
        $options->graderinfoformat = $formdata->graderinfo['format'];
        /* Fileresponse doesn't display a response template. */
        $options->responsetemplate = '';
        /* Fileresponse doesn't display a response template. */
        $options->responsetemplateformat = FORMAT_HTML;
        $DB->update_record('qtype_fileresponse_options', $options);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        /* Fileresponse only accepts 'plain' as format. */
        $question->responseformat = 'plain';
        $question->responsefieldlines = $questiondata->options->responsefieldlines;
        $question->attachments = $questiondata->options->attachments;
        $question->forcedownload = $questiondata->options->forcedownload;
        $question->allowpickerplugins = $questiondata->options->allowpickerplugins;
        $question->graderinfo = $questiondata->options->graderinfo;
        $question->graderinfoformat = $questiondata->options->graderinfoformat;
        /* Fileresponse doesn't display a response template. */
        $question->responsetemplate = '';
        /* Fileresponse doesn't display a response template. */
        $question->responsetemplateformat = FORMAT_HTML;
        $filetypesutil = new \core_form\filetypes_util();
        $question->filetypeslist = $filetypesutil->normalize_file_types($questiondata->options->filetypeslist);
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
        /* Fileresponse only accepts 'plain' as format. */
        return array(
            'plain' => get_string('formatplain', 'qtype_fileresponse')
        );
    }

    /**
     * @return array the choices that should be offered when asking if a response is required
     */
    public function response_required_options() {
        return array(
            1 => get_string('responseisrequired', 'qtype_fileresponse'),
            0 => get_string('responsenotrequired', 'qtype_fileresponse'),
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
            /* Fileresponse has to have at least one file required. */
            1 => '1',
            2 => '2',
            3 => '3',
            -1 => get_string('unlimited'),
        );
    }

    /**
     * @return array the choices that should be offered for the number of required attachments.
     */
    public function attachments_required_options() {
        return array(
            0 => get_string('attachmentsoptional', 'qtype_fileresponse'),
            1 => '1',
            2 => '2',
            3 => '3'
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

    /**
     * Provide export functionality for xml format.
     *
     * @param question object the question object
     * @param format object the format object so that helper methods can be used
     * @param extra mixed any additional format specific data that may be passed by the format (see
     *        format code for info)
     *
     * @return string the data to append to the output buffer or false if error
     */
    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        $expout = '';
        $fs = get_file_storage();
        $contextid = $question->contextid;

        // Set the additional fields.
        $expout .= '    <responseformat>' . $question->options->responseformat .
            "</responseformat>\n";
        $expout .= '    <responsefieldlines>' . $question->options->responsefieldlines .
            "</responsefieldlines>\n";
        $expout .= '    <attachments>' . $question->options->attachments .
            "</attachments>\n";
        $expout .= '    <forcedownload>' . $question->options->forcedownload .
            "</forcedownload>\n";
        $expout .= '    <allowpickerplugins>' . $question->options->allowpickerplugins .
            "</allowpickerplugins>\n";
        $files = $fs->get_area_files($contextid, 'qtype_fileresponse', 'graderinfo', $question->id);
        $expout .= '    <graderinfo format="'.$question->options->graderinfoformat.'">' . $format->writetext($question->options->graderinfo);
        $expout .= $format->write_files($files);
        $expout .= "</graderinfo>\n";
        $expout .= '    <graderinfoformat>' . $question->options->graderinfoformat .
            "</graderinfoformat>\n";

        return $expout;
    }

    /**
     * Provide import functionality for xml format.
     *
     * @param data mixed the segment of data containing the question
     * @param question object question object processed (so far) by standard import code
     * @param format object the format object so that helper methods can be used (in particular
     *        error())
     * @param extra mixed any additional format specific data that may be passed by the format (see
     *        format code for info)
     *
     * @return object question object suitable for save_options() call or false if cannot handle
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        // Check whether the question is for us.
        if (!isset($data['@']['type']) || $data['@']['type'] != 'fileresponse') {
            return false;
        }

        $question = $format->import_headers($data);
        $question->qtype = 'fileresponse';

        $question->responseformat = $format->getpath($data,
            array('#', 'responseformat', 0, '#', 'text', 0, '#'
            ), 'plain');
        $question->responsefieldlines = $format->getpath($data,
            array('#', 'responsefieldlines', 0, '#'
            ), 0);
        $question->attachments = $format->getpath($data,
            array('#', 'attachments', 0, '#'
            ), 0);
        $question->forcedownload = $format->getpath($data,
            array('#', 'forcedownload', 0, '#'
            ), 0);
        $question->allowpickerplugins = $format->getpath($data,
            array('#', 'allowpickerplugins', 0, '#'
            ), 0);
        $question->graderinfo = array();
        $question->graderinfo['text'] = $format->getpath($data,
            array('#', 'graderinfo', 0, '#', 'text', 0, '#'
            ), '', true);
        $question->graderinfo['format'] = $format->getpath($data,
            array('#', 'graderinfo', 0, '@', 'format'), 1);
        // Restore files in graderinfo.
        $files = $format->getpath($data, array('#', 'graderinfo', 0, '#', 'file'
        ), array(), false);
        foreach ($files as $file) {
            $filesdata = new stdclass();
            $filesdata->content = $file['#'];
            $filesdata->encoding = $file['@']['encoding'];
            $filesdata->name = $file['@']['name'];
            $question->graderinfo['files'][] = $filesdata;
        }
        $question->graderinfoformat = $format->getpath($data, array('#', 'graderinfoformat', 0, '#'), 1);
        return $question;
    }
}
