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
 * Test helpers for the fileresponse question type.
 *
 * @package    qtype_fileresponse
 * @copyright  2022 Luca Bösch, BFH Bern University of Applied Sciences luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable Generic.Arrays.DisallowLongArraySyntax
// phpcs:disable moodle.Commenting.MissingDocblock.Function

/**
 * Test helper class for the fileresponse question type.
 *
 * @copyright  2022 Luca Bösch, BFH Bern University of Applied Sciences luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_fileresponse_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('allowdownload_allowfilepicker', 'allowdownload_disallowfilepicker', 'disallowdownload_allowfilepicker',
            'disallowdownload_disallowfilepicker');
    }

    /**
     * Helper method to reduce duplication.
     * @return qtype_fileresponse_question
     */
    protected function initialise_fileresponse_question() {
        question_bank::load_question_definition_classes('fileresponse');
        $q = new qtype_fileresponse_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'File Response question';
        $q->questiontext = 'Please upload a file/some files.';
        $q->generalfeedback = 'I hope your file(s) was/were okay.';
        $q->responsefieldlines = 10;
        $q->attachments = 1;
        $q->filetypeslist = '';
        $q->forcedownload = 0;
        $q->allowpickerplugins = 1;
        $q->graderinfo = '';
        $q->graderinfoformat = FORMAT_HTML;
        $q->qtype = question_bank::get_qtype('fileresponse');

        return $q;
    }

    /**
     * Makes a fileresponse question allowing filedownload and allowing file picker plugins.
     * @return qtype_fileresponse_question
     */
    public function make_fileresponse_question_allowdownload_allowfilepicker() {
        return $this->initialise_fileresponse_question();
    }

    /**
     * Make the data what would be received from the editing form for a fileresponse
     * question using the HTML editor allowing file picker plugins, allowing file download and expecting
     * three expected attachments.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public function get_fileresponse_question_form_data_allowdownload_allowfilepicker() {
        $fromform = new stdClass();

        $fromform->name = 'File Response question (allow download, allow picker plugins)';
        $fromform->questiontext = array('text' => 'Please upload three files.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'I hope your files were okay.',
            'format' => FORMAT_HTML);
        $fromform->responsefieldlines = 10;
        $fromform->attachments = 1;
        $fromform->allowpickerplugins = 1;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsetemplate = array('text' => '', 'format' => FORMAT_HTML);

        return $fromform;
    }

    /**
     * Make the data what would be received from the editing form for a fileresponse
     * question using the HTML editor allowing file picker plugins, disallowing file download and expecting
     * two expected attachments.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public function get_fileresponse_question_form_data_disallowdownload_allowfilepicker() {
        $fromform = new stdClass();

        $fromform->name = 'File Response question (disallow download, allow picker plugins)';
        $fromform->questiontext = array('text' => 'Please upload two files.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'I hope your files were okay.',
            'format' => FORMAT_HTML);
        $fromform->responsefieldlines = 10;
        $fromform->attachments = 2;
        $fromform->forcedownload = 1;
        $fromform->allowpickerplugins = 1;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsetemplate = array('text' => '', 'format' => FORMAT_HTML);

        return $fromform;
    }

    /**
     * Makes a fileresponse question disallowing filedownload and sallowing file picker plugins.
     * @return qtype_fileresponse_question
     */
    public function make_fileresponse_question_disallowdownload_allowfilepicker() {
        $q = $this->initialise_fileresponse_question();
        $q->forcedownload = 1;
        $q->allowpickerplugins = 1;
        return $q;
    }

    /**
     * Make the data what would be received from the editing form for a fileresponse
     * question using the HTML editor disallowing file picker plugins, disallowing file download and expecting
     * one expected attachments.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public function get_fileresponse_question_form_data_disallowdownload_disallowfilepicker() {
        $fromform = new stdClass();

        $fromform->name = 'File Response question (disallow download, disallow picker plugins)';
        $fromform->questiontext = array('text' => 'Please upload a single file.', 'format' => FORMAT_HTML);
        $fromform->defaultmark = 1.0;
        $fromform->generalfeedback = array('text' => 'I hope your file was okay.',
            'format' => FORMAT_HTML);
        $fromform->responsefieldlines = 10;
        $fromform->attachments = 1;
        $fromform->forcedownload = 1;
        $fromform->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $fromform->responsetemplate = array('text' => '', 'format' => FORMAT_HTML);

        return $fromform;
    }

    /**
     * Makes a fileresponse question allowing filedownload and disallowing file picker plugins.
     * @return qtype_fileresponse_question
     */
    public function make_fileresponse_question_allowdownload_disallowfilepicker() {
        $q = $this->initialise_fileresponse_question();
        $q->allowpickerplugins = 0;
        return $q;
    }

    /**
     * Makes a fileresponse question disallowing filedownload and disallowing file picker plugins.
     * @return qtype_fileresponse_question
     */
    public function make_fileresponse_question_disallowdownload_disallowfilepicker() {
        $q = $this->initialise_fileresponse_question();
        $q->forcedownload = 1;
        $q->allowpickerplugins = 0;
        return $q;
    }
}
