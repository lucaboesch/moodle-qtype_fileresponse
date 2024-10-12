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
 * Upgrade library code for the fileresponse question type.
 *
 * @package    qtype_fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class for converting attempt data for fileresponse questions when upgrading
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_fileresponse_qe2_attempt_updater extends question_qtype_attempt_updater {
    #[\Override]
    public function right_answer() {
        return '';
    }

    #[\Override]
    public function response_summary($state) {
        if (!empty($state->answer)) {
            return $this->to_text($state->answer);
        } else {
            return null;
        }
    }

    #[\Override]
    public function was_answered($state) {
        return !empty($state->answer);
    }

    #[\Override]
    public function set_first_step_data_elements($state, &$data) {
    }

    #[\Override]
    public function supply_missing_first_step_data(&$data) {
    }

    #[\Override]
    public function set_data_elements_for_step($state, &$data) {
        if (!empty($state->answer)) {
            $data['answer'] = $state->answer;
            $data['answerformat'] = FORMAT_HTML;
        }
    }
}
