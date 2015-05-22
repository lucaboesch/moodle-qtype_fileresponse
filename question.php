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
 * File response question definition class.
 *
 * @package    qtype
 * @subpackage fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Represents an fileresponse question.
 *
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_fileresponse_question extends question_with_responses {

    public $responseformat;
    public $responsefieldlines;
    public $attachments;
    public $forcedownload;
    public $allowpickerplugins;
    public $graderinfo;
    public $graderinfoformat;
    public $logfile = "/home/adm-bsl3/log.txt";

    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        question_engine::load_behaviour_class('manualgraded');
        return new qbehaviour_manualgraded($qa, $preferredbehaviour);
    }

    /**
     * @param moodle_page the page we are outputting to.
     * @return qtype_fileresponse_format_renderer_base the response-format-specific renderer.
     */
    public function get_format_renderer(moodle_page $page) {
        return $page->get_renderer('qtype_fileresponse', 'format_' . $this->responseformat);
    }

    public function get_expected_data() {
        /* fileresponse only accepts 'formatplain' as format */
        if ($this->responseformat == 'editorfilepicker') {
            $expecteddata = array('answer' => question_attempt::PARAM_CLEANHTML_FILES);
        } else {
            $expecteddata = array('answer' => PARAM_CLEANHTML);
        }
        $expecteddata['answerformat'] = PARAM_FORMAT;
        if ($this->attachments != 0) {
            $expecteddata['attachments'] = question_attempt::PARAM_FILES;
        }
        return $expecteddata;
    }

    public function summarise_response(array $response) {
        /* It would be nice to have the answer text but a list of the files' uploaded names as well */
        if (isset($response['answer'])) {
            $formatoptions = new stdClass();
            $formatoptions->para = false;
            return html_to_text(format_text($response['answer'], FORMAT_HTML, $formatoptions), 0, false);
        } else {
            return null;
        }
    }

    public function get_correct_response() {
        return null;
    }

    public function get_question_file_saver_value(array $response) {
        $question_file_saver_value = null;
        foreach ($response as $name => $value) {
            if ($value instanceof question_file_saver) {
                $question_file_saver_value = (string) $value;
                return $question_file_saver_value;
            }
        }
        return "";
    }

    public function get_question_attemptstepid(array $response, $question_file_saver_value) {
        $log = 'get_question_attemptstepid called with array $response';
        $log .= "\n";
        $log .= print_r($response, true);
        $log .= "\n";
        $log .= 'and $question_file_saver_value ';
        $log .= $question_file_saver_value;
        $log .= "\n";
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        fwrite($fh, $log);

        /* should return the questions's attempt step id. does not work reliably */
        global $CFG, $DB;

        $attemptstepparams = array('value' => $question_file_saver_value);
        $attemptstepsql = "SELECT attemptstepid FROM {question_attempt_step_data} WHERE name = 'attachments' AND value = :value";
        $log = "MySQL Query:\nSELECT attemptstepid FROM mdl_question_attempt_step_data WHERE name = 'attachments' AND value = '$question_file_saver_value'\nMySQL Result:\n";
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        $log .= print_r($DB->get_field_sql($attemptstepsql, $attemptstepparams), true);
        $log .= "\n";
        
        /* race condition here! wait for datalib.php question_engine_data_mapper insert_question_attempt */

        $get_field_sql_result = $DB->get_field_sql($attemptstepsql, $attemptstepparams);

        /* kläglicher Versuch: 
         * 
        while (!$this->isAttemptStepIDThere($question_file_saver_value)) {
            $attemptstepparams = array('value' => $question_file_saver_value);
            $attemptstepsql = "SELECT attemptstepid FROM {question_attempt_step_data} WHERE name = 'attachments' AND value = :value";
            $log = "MySQL Query:\nSELECT attemptstepid FROM mdl_question_attempt_step_data WHERE name = 'attachments' AND value = '$question_file_saver_value'\nMySQL Result:\n";
            $fh = fopen($this->logfile, 'a') or die("can't open file");
            $log .= print_r($DB->get_field_sql($attemptstepsql, $attemptstepparams), true);
            $log .= "\n";
            $get_field_sql_result = $DB->get_field_sql($attemptstepsql, $attemptstepparams);
        }
         * 
         * geht leider nicht. Wo wird die $attemptstepid gebildet und in die DB eingesetzt? 
        */
        
        if (empty($get_field_sql_result)) {
            $log .= "die attemptstepid gibt es noch nicht.\n";
        }
        fwrite($fh, $log);
        /* hier kann geschehen: die attemptstepid gibt es noch gar nicht. oder es werden mehrere SQL-Resultatzeilen herausgegeben (mit identischer attemptstepid */
        return $DB->get_field_sql($attemptstepsql, $attemptstepparams);
    }

        function isAttemptStepIDThere($code) {
        global $CFG, $DB;
        $log = "";
        $attemptstepparams = array('value' => $code);
        $attemptstepsql = "SELECT attemptstepid FROM {question_attempt_step_data} WHERE name = 'attachments' AND value = :value";
        $get_field_sql_result = $DB->get_field_sql($attemptstepsql, $attemptstepparams);
        if (empty($get_field_sql_result)) {
            $log .= "die attemptstepid gibt es noch nicht. ".time()."\n";
            $fh = fopen($this->logfile, 'a') or die("can't open file");
            fwrite($fh, $log);
            $log = "";
            return false;
        } else {
            $log .= "die attemptstepid gibt es nun.".time()."\n";
            $fh = fopen($this->logfile, 'a') or die("can't open file");
            fwrite($fh, $log);
            $log = "";
            return true;
        }
        return true;
    }
    
    public function get_attemptstepid_questionid($attemptstepid) {
        $log = "\n";
        $log .= 'get_attemptstepid_questionid called with $attemptstepid';
        $log .= "\n";
        $log .= $attemptstepid;
        $log .= "\n";
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        fwrite($fh, $log);

        global $CFG, $DB;
        $attemptstepparams = array('value' => $attemptstepid);
        $attemptstepsql = "SELECT {question_attempts}.questionid from {question_attempts} JOIN {question_attempt_steps}
            WHERE {question_attempt_steps}.questionattemptid = {question_attempts}.id  AND {question_attempt_steps}.id = :value";

        $log = "MySQL Query:\nSELECT mdl_question_attempts.questionid from mdl_question_attempts JOIN mdl_question_attempt_steps
            WHERE mdl_question_attempt_steps.questionattemptid = mdl_question_attempts.id  AND mdl_question_attempt_steps.id = $attemptstepid\nMySQL Result:\n";
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        $log .= print_r($DB->get_field_sql($attemptstepsql, $attemptstepparams), true);
        $log .= "\n";
        $get_field_sql = $DB->get_field_sql($attemptstepsql, $attemptstepparams);
        if (empty($get_field_sql)) {
            $log .= "die questionid gibt es noch nicht.\n";
        }
        fwrite($fh, $log);

        return $DB->get_field_sql($attemptstepsql, $attemptstepparams);
    }

    public function get_question_userid(array $response, $question_attemptstepid) {
        $log = "\n";
        $log .= 'get_question_userid called with array $response';
        $log .= "\n";
        $log .= print_r($response, true);
        $log .= "\n";
        $log .= "and ";
        $log .= '$question_attemptstepid';
        $log .= "\n";
        $log .= $question_attemptstepid;
        $log .= "\n";
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        fwrite($fh, $log);
        /* should return the userid of the user which fills in this questions's attempt step. does not work reliably, since the parameter $question_attemptstepid often comes in as NULL */
        global $CFG, $DB;

        $useridparams = array('value' => $question_attemptstepid);
        $useridsql = "SELECT userid FROM {question_attempt_steps} WHERE id = :value";
        $log = "MySQL Query:\nSELECT userid FROM mdl_question_attempt_steps WHERE id = $question_attemptstepid\nMySQL Result:\n";

        $fh = fopen($this->logfile, 'a') or die("can't open file");
        $log .= print_r($DB->get_field_sql($useridsql, $useridparams), true);
        $log .= "\n";
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        fwrite($fh, $log);
        return $DB->get_field_sql($useridsql, $useridparams);
    }

    public function amount_required_files() {
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        fwrite($fh, $log);
        return $this->attachments;
    }

    /* how many attached files there are in this question_attempt_step */

    public function amount_attached_files(array $response) {
        global $CFG, $DB, $_REQUEST;
        /* if the is_complete_response call comes from review.php for example $response has a 
          [attachments] => eb4ad3b5bbf0fd91cf8977eed9f7ee23
          instead of an [attachments] => question_file_saver Object
         */
        $amount_attached_files = 0;
        $log = "\n";
        $log .= 'amount_attached_files called with array $response';
        $log .= "\n";
        $log .= print_r($response, true);
        $log .= "\n";

        /* is $response empty ? */
        if (!(count($response))) {
            /* $response is empty */
            $log .= "there are no files\n";
            $fh = fopen($this->logfile, 'a') or die("can't open file");
            fwrite($fh, $log);
            return $amount_attached_files; /* $response is empty */
        } else {
            /* $response is not empty */
            /* has $response a ['attachments'] key ? */
            if (array_key_exists('attachments', $response)) {
                /* $response has an ['attachments'] key */
                if ((get_class($response['attachments'])) == 'question_file_saver') {
                    /* $response['attachments'] is a question_file_saver Object */
                    if ($this->get_question_file_saver_value($response) == '') {
                        $log .= "no question_file_saver->value!\n";
                        $fh = fopen($this->logfile, 'a') or die("can't open file");
                        fwrite($fh, $log);
                        return $amount_attached_files;
                    } else {
                        $log .= 'amount_attached_files knows value=' . $this->get_question_file_saver_value($response) . "\n";
                        $fh = fopen($this->logfile, 'a') or die("can't open file");
                        fwrite($fh, $log);
                        $attemptstepid = $this->get_question_attemptstepid($response, $this->get_question_file_saver_value($response));

                        $log = 'amount_attached_files knows attemptstepid=' . $attemptstepid . "\n";
                        $fh = fopen($this->logfile, 'a') or die("can't open file");
                        fwrite($fh, $log);

                        $questionid = $this->get_attemptstepid_questionid($attemptstepid);
                        $log = 'amount_attached_files knows $questionid=' . $questionid . "\n";
                        $fh = fopen($this->logfile, 'a') or die("can't open file");
                        fwrite($fh, $log);

                        $userid = $this->get_question_userid($response, $attemptstepid);
                        $log = 'amount_attached_files knows $userid=' . $userid . "\n";
                        $fh = fopen($this->logfile, 'a') or die("can't open file");
                        fwrite($fh, $log);

                        $fs = get_file_storage();

                        $usercontext = get_context_instance(CONTEXT_USER, $userid);
                        $log = 'amount_attached_files knows $usercontext->id=' . $usercontext->id . "\n";

                        $i = 0;

                        /* find a key in array $_REQUEST which ends with "_attachments" */
                        $draftitemidfound = FALSE;
                        foreach (array_keys($_REQUEST) as $key) {
                            if (strstr($key, "_attachments")) {
                                $draftitemid = $_REQUEST[$key];
                                $log .= "The key is $key\n";
                                $log .= "draftitemid=$draftitemid\n";
                                $draftitemidfound = TRUE;
                            }
                        }
                        if (!$draftitemidfound) {
                                $log .= "draftitemid unknown\n";
                        }

                        if (!is_null($questionid) and $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false)) {
                            foreach ($files as $file) {
//                    $log .= $i . "\n";
                                $i++;
                                $amount_attached_files++;
                                if ($file->is_directory() and $file->get_filepath() === '/') {
                                    // we need a way to mark the age of each draft area,
                                    // by not copying the root dir we force it to be created automatically with current timestamp
                                    continue;
                                }
                                if (!$options['subdirs'] and ($file->is_directory() or $file->get_filepath() !== '/')) {
                                    continue;
                                }
                            }
                        }
                        $log .= "There are $i uploaded files.\n";

                        if (!is_null($text)) {
                            // at this point there should not be any draftfile links yet,
                            // because this is a new text from database that should still contain the @@pluginfile@@ links
                            // this happens when developers forget to post process the text
                            $text = str_replace("\"$CFG->httpswwwroot/draftfile.php", "\"$CFG->httpswwwroot/brokenfile.php#", $text);
                        }

                        $log .= "\n\n";
                        $fh = fopen($this->logfile, 'a') or die("can't open file");
                        fwrite($fh, $log);
                        return $amount_attached_files;
                    }
                } else {
                    /* $response['attachments'] is not a question_file_saver Object but a value string */
                    $log = 'value=' . $response['attachments'] . "\n";

                    $attemptstepid = $this->get_question_attemptstepid($response, $response['attachments']);
                    $log .= 'attemptstepid=' . $attemptstepid . "\n";

                    $questionid = $this->get_attemptstepid_questionid($attemptstepid);
                    $log .= '$questionid=' . $questionid . "\n";

                    $userid = $this->get_question_userid($response, $attemptstepid);
                    $log .= '$userid=' . $userid . "\n";

                    $fs = get_file_storage();

                    $usercontext = get_context_instance(CONTEXT_USER, $userid);
                    $log .= '$usercontext->id=' . $usercontext->id . "\n";

                    $fh = fopen($this->logfile, 'a') or die("can't open file");
                    fwrite($fh, $log);
                }
            } else {
                /* $response has no ['attachments'] key */
                $log .= "there are no files\n";
                $fh = fopen($this->logfile, 'a') or die("can't open file");
                fwrite($fh, $log);
                return $amount_attached_files;
            }
        }
    }

    public function is_complete_response(array $response) {
        $log = "------------------------------------------------------------------------------------------------------------------\n";
        $log .= 'is_complete_response called with array $response';
        $log .= "\n";
        $log .= print_r($response, true);
        $log .= "\n";
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        fwrite($fh, $log);

        /* passed array overview */


        /* is $response empty ? */
        if (!(count($response))) {
            /* $response is empty */
            $log = '* $response is empty *';
            $log .= "\nthere are no files\n";
            $fh = fopen($this->logfile, 'a') or die("can't open file");
            fwrite($fh, $log);
            return $amount_attached_files; /* $response is empty */
        } else {
            /* $response is not empty */
            /* has $response a ['attachments'] key ? */
            if (array_key_exists('attachments', $response)) {
                /* $response has an ['attachments'] key */
                $log = '$response has an [\'attachments\'] key';
                $log .= "\n";
                $fh = fopen($this->logfile, 'a') or die("can't open file");
                fwrite($fh, $log);
                if ((get_class($response['attachments'])) == 'question_file_saver') {
                    /* $response['attachments'] is a question_file_saver Object */
                    $log = '$response[\'attachments\'] is a question_file_saver Object';
                    $log .= "\n";
                    $fh = fopen($this->logfile, 'a') or die("can't open file");
                    fwrite($fh, $log);
                    if ($this->get_question_file_saver_value($response) == '') {
                        $log = '$response[\'attachments\'] has no question_file_saver->value!';
                        $log .= "\n";
                        $fh = fopen($this->logfile, 'a') or die("can't open file");
                        fwrite($fh, $log);
                        return $amount_attached_files;
                    } else {
                        $log = '$response[\'attachments\'] has a question_file_saver->value';
                        $log .= "\n";
                        $fh = fopen($this->logfile, 'a') or die("can't open file");
                        fwrite($fh, $log);
                    }
                } else {
                    /* $response['attachments'] is not a question_file_saver Object but a value string */
                    $log = '$response[\'attachments\'] is not a question_file_saver Object but a value string';
                    $log .= "\n";
                    $fh = fopen($this->logfile, 'a') or die("can't open file");
                    fwrite($fh, $log);
                }
            } else {
                /* $response has no ['attachments'] key */
                $log = '$response has no [\'attachments\'] key';
                $log .= "\n";
                $fh = fopen($this->logfile, 'a') or die("can't open file");
                fwrite($fh, $log);
                return $amount_attached_files;
            }
        }
                /* $response has no ['attachments'] key */
                $log = "\n";
                $log .= "\n";
                $fh = fopen($this->logfile, 'a') or die("can't open file");
                fwrite($fh, $log);

        /* and here the function begins */

        $required = $this->amount_required_files($response);



        $actual = $this->amount_attached_files($response);
        $log = "required files: $required, actual files: $actual\n\n";
        $fh = fopen($this->logfile, 'a') or die("can't open file");
        fwrite($fh, $log);
        if ($actual <= 0) { /* no files */
            return false;
        } else {
            if ($required == -1) { /* number of expected files unlimited */
                return true; /* there is almost one file */
            } else { /* precise number of expected files */
                if ($actual >= $required) {
                    return true; /* there is the expected amount of files (or more) */
                } else {
                    return false; /* there isn't the expected amount of files */
                }
            }
        }
        return true;
        /* fileupload question type is always complete (i.e. ready for grading) */
        /* always returns true, since from this point, there is no possibility though to the question_attempt_step and its fileuploader */
        return true;
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        /* fileupload question responses are never the same */
        /* should return true when there is an answer, and the same answer in the comment field (or both answers are empty and the uploaded files are identical */
        /* always returns false, since from this point, there is no possibility though to the question_attempt_step and its fileuploader */
        return false;
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'response_attachments') {
            // Response attachments visible if the question has them.
            return $this->attachments != 0;
        } else if ($component == 'question' && $filearea == 'response_answer') {
            // Response attachments visible if the question has them.
            return $this->responseformat === 'editorfilepicker';
        } else if ($component == 'qtype_fileresponse' && $filearea == 'graderinfo') {
            return $options->manualcomment;
        } else {
            return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
        }
    }

    public function classify_response(array $response) {
        return array();
    }

}
