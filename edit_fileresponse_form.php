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
 * Defines the editing form for the fileresponse question type.
 *
 * @package    qtype_fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Fileresponse question type editing form.
 *
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_fileresponse_edit_form extends question_edit_form {


    protected function definition_inner($mform) {
        $qtype = question_bank::get_qtype('fileresponse');

        /* Display the ?forcedownload=1 advice. */
        $mform->addElement('static','advice', get_string('advice', 'qtype_fileresponse'),'<div style="width:496px;">'.get_string('questiontextforcedownload', 'qtype_fileresponse').'</div>');

        /* Fileresponse only accepts 'plain' as format */
        $mform->setDefault('responseformat', 'plain');

        $mform->addElement('header', 'responseoptions', get_string('responseoptions', 'qtype_fileresponse'));
        $mform->setExpanded('responseoptions');

        /* Response format element removed from qtype_essay. */

        /* Response required element removed from qtype_essay. */

        $mform->addElement('select', 'responsefieldlines',
            get_string('responsefieldlines', 'qtype_fileresponse'), $qtype->response_sizes());
        $mform->setDefault('responsefieldlines', 15);

        /* Fileresponse has to have at least one file required */
        $mform->addElement('select', 'attachments',
            get_string('amountofexpectedfiles', 'qtype_fileresponse'), $qtype->attachment_options());
        $mform->setDefault('attachments', 1);

        /* Attachment required element removed from qtype_essay. */

        $mform->addElement('filetypes', 'filetypeslist', get_string('acceptedfiletypes', 'qtype_fileresponse'));
        $mform->addHelpButton('filetypeslist', 'acceptedfiletypes', 'qtype_fileresponse');
        $mform->disabledIf('filetypeslist', 'attachments', 'eq', 0);

        /* The element to allow or disallow repositories. */
        $mform->addElement('select', 'forcedownload',
            get_string('forcedownload', 'qtype_fileresponse'), $qtype->forcedownload_options());
        $mform->setDefault('forcedownload', 0);

        /* The element to allow or disallow repositories. */
        $mform->addElement('select', 'allowpickerplugins',
            get_string('allowpickerplugins', 'qtype_fileresponse'), $qtype->allowpickerplugins_options());
        $mform->setDefault('allowpickerplugins', 0);

        /* Response template element removed from qtype_essay. */

        $mform->addElement('header', 'graderinfoheader', get_string('graderinfoheader', 'qtype_fileresponse'));
        $mform->setExpanded('graderinfoheader');
        $mform->addElement('editor', 'graderinfo', get_string('graderinfo', 'qtype_fileresponse'),
            array('rows' => 10), $this->editoroptions);
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (empty($question->options)) {
            return $question;
        }

        /* Fileresponse only accepts format 'plain' as format. */
        $question->responseformat = 'plain';
        $question->responsefieldlines = $question->options->responsefieldlines;
        $question->attachments = $question->options->attachments;
        $question->filetypeslist = $question->options->filetypeslist;
        $question->forcedownload = $question->options->forcedownload;
        $question->allowpickerplugins = $question->options->allowpickerplugins;

        $draftid = file_get_submitted_draft_itemid('graderinfo');
        $question->graderinfo = array();
        $question->graderinfo['text'] = file_prepare_draft_area(
            $draftid,           // Draftid
            $this->context->id, // context
            'qtype_fileresponse',      // component
            'graderinfo',       // filarea
            !empty($question->id) ? (int) $question->id : null, // itemid
            $this->fileoptions, // options
            $question->options->graderinfo // text.
        );
        $question->graderinfo['format'] = $question->options->graderinfoformat;
        $question->graderinfo['itemid'] = $draftid;

        /* Fileresponse doesn't display a response template. */
        $question->responsetemplate = array(
            'text' => '',
            'format' => FORMAT_HTML,
        );

        return $question;
    }

    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        // Don't allow the teacher to require more attachments than they allow; as this would
        // create a condition that it's impossible for the student to meet.
        if ((isset($fromform['attachmentsrequired'])) &&
            ($fromform['attachments'] != -1 && $fromform['attachments'] < $fromform['attachmentsrequired'])) {
                $errors['attachmentsrequired']  = get_string('mustrequirefewer', 'qtype_fileresponse');
        }

        return $errors;
    }

    public function qtype() {
        return 'fileresponse';
    }
}
