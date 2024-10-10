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
 * This file contains tests that walks fileresponse questions through some attempts.
 *
 * @package    qtype_fileresponse
 * @copyright  2022 Luca Bösch, BFH Bern University of Applied Sciences luca.boesch@bfh.ch
 * @copyright  based on work by 2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_fileresponse;
use question_bank;
use question_engine;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the fileresponse question type.
 *
 * @package    qtype_fileresponse
 * @copyright  2022 Luca Bösch, BFH Bern University of Applied Sciences luca.boesch@bfh.ch
 * @copyright  based on work by 2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    public function test_deferred_feedback_allowdownload_allowfilepicker(): void {
        global $CFG, $USER, $PAGE;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        // Required to init a text editor.
        $PAGE->set_url('/');
        $usercontextid = \context_user::instance($USER->id)->id;
        $fs = get_file_storage();

        // Create an fileresponse question in the DB.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('fileresponse', 'allowdownload_allowfilepicker', ['category' => $cat->id]);

        // Start attempt at the question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        // First we need to get the draft item ids.
        $this->render();

        if (!preg_match('/env=fileresponsefilemanager&amp;action=browse&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new \coding_exception('File manager draft item id not found.');
        }
        $attachementsdraftid = $matches[1];

        $this->save_file_to_draft_area($usercontextid, $attachementsdraftid, 'greeting.txt', 'Hello world!');
        $this->process_submission([
            'answer' => 'Here is a file.',
            'answerformat' => FORMAT_PLAIN,
            'attachments' => $attachementsdraftid]);

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Save the same response again, and verify no new step is created.
        $this->load_quba();

        $this->render();
        if (!preg_match('/env=fileresponsefilemanager&amp;action=browse&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new \coding_exception('File manager draft item id not found.');
        }
        $attachementsdraftid = $matches[1];

        $this->process_submission([
            'answer' => 'Here is a file.',
            'answerformat' => FORMAT_PLAIN,
            'attachments' => $attachementsdraftid]);

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);

        // Now submit all and finish.
        $this->finish();
        $this->check_current_state(question_state::$needsgrading);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now start a new attempt based on the old one.
        $this->load_quba();
        $oldqa = $this->get_question_attempt();

        $q = question_bank::load_question($question->id);
        $this->quba = question_engine::make_questions_usage_by_activity('unit_test',
            \context_system::instance());
        $this->quba->set_preferred_behaviour('deferredfeedback');
        $this->slot = $this->quba->add_question($q, 1);
        $this->quba->start_question_based_on($this->slot, $oldqa);

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->save_quba();

        // Now save the same response again, and ensure that a new step is not created.
        $this->load_quba();

        $this->render();
        if (!preg_match('/env=fileresponsefilemanager&amp;action=browse&amp;.*?itemid=(\d+)&amp;/', $this->currentoutput, $matches)) {
            throw new \coding_exception('File manager draft item id not found.');
        }
        $attachementsdraftid = $matches[1];

        $this->process_submission([
            'answer' => 'Here is a file.',
            'answerformat' => FORMAT_PLAIN,
            'attachments' => $attachementsdraftid]);

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(1);
    }

    /**
     * Helper method: Store a test file with a given name and contents in a
     * draft file area.
     *
     * @param int $usercontextid user context id.
     * @param int $draftitemid draft item id.
     * @param string $filename filename.
     * @param string $contents file contents.
     */
    protected function save_file_to_draft_area($usercontextid, $draftitemid, $filename, $contents): void {
        $fs = get_file_storage();

        $filerecord = new \stdClass();
        $filerecord->contextid = $usercontextid;
        $filerecord->component = 'user';
        $filerecord->filearea = 'draft';
        $filerecord->itemid = $draftitemid;
        $filerecord->filepath = '/';
        $filerecord->filename = $filename;
        $fs->create_file_from_string($filerecord, $contents);
    }

}
