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
 * Fileresponse question type upgrade code.
 *
 * @package    qtype
 * @subpackage fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the fileresponse question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_fileresponse_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011031000) {
        // Define table qtype_fileresponse_options to be created
        $table = new xmldb_table('qtype_fileresponse_options');

        // Adding fields to table qtype_fileresponse_options
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);
        $table->add_field('responseformat', XMLDB_TYPE_CHAR, '16', null,
                XMLDB_NOTNULL, null, 'plain');
        $table->add_field('responsefieldlines', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '15');
        $table->add_field('attachments', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('forcedownload', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '1');
        $table->add_field('allowpickerplugins', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('graderinfo', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null);
        $table->add_field('graderinfoformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');

        // Adding keys to table qtype_fileresponse_options
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN_UNIQUE,
                array('questionid'), 'question', array('id'));

        // Conditionally launch create table for qtype_fileresponse_options
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // fileresponse savepoint reached
        upgrade_plugin_savepoint(true, 2011031000, 'qtype', 'fileresponse');
    }

    if ($oldversion < 2011060300) {
        // Insert a row into the qtype_fileresponse_options table for each existing fileresponse question.
        $DB->execute("
                INSERT INTO {qtype_fileresponse_options} (questionid, responseformat,
                        responsefieldlines, attachments, graderinfo, graderinfoformat)
                SELECT q.id, 'plain', 15, 0, '', " . FORMAT_MOODLE . "
                FROM {question} q
                WHERE q.qtype = 'fileresponse'
                AND NOT EXISTS (
                    SELECT 'x'
                    FROM {qtype_fileresponse_options} qeo
                    WHERE qeo.questionid = q.id)");

        // fileresponse savepoint reached
        upgrade_plugin_savepoint(true, 2011060300, 'qtype', 'fileresponse');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2011102701) {
        // In Moodle <= 2.0 fileresponse had both question.generalfeedback and question_answers.feedback.
        // This was silly, and in Moodle >= 2.1 only question.generalfeedback. To avoid
        // dataloss, we concatenate question_answers.feedback onto the end of question.generalfeedback.
        $toupdate = $DB->get_recordset_sql("
                SELECT q.id,
                       q.generalfeedback,
                       q.generalfeedbackformat,
                       qa.feedback,
                       qa.feedbackformat

                  FROM {question} q
                  JOIN {question_answers} qa ON qa.question = q.id

                 WHERE q.qtype = 'fileresponse'
                   AND " . $DB->sql_isnotempty('question_answers', 'feedback', false, true));

            foreach ($toupdate as $data) {
                $progressbar->update($done, $count, "Updating fileresponse feedback ({$done}/{$count}).");
                upgrade_set_timeout(60);
                if ($data->generalfeedbackformat == $data->feedbackformat) {
                    $DB->set_field('question', 'generalfeedback',
                            $data->generalfeedback . $data->feedback,
                            array('id' => $data->id));

                } else {
                    $newdata = new stdClass();
                    $newdata->id = $data->id;
                    $newdata->generalfeedback =
                            qtype_fileresponse_convert_to_html($data->generalfeedback, $data->generalfeedbackformat) .
                            qtype_fileresponse_convert_to_html($data->feedback,        $data->feedbackformat);
                    $newdata->generalfeedbackformat = FORMAT_HTML;
                    $DB->update_record('question', $newdata);
                }
            }

            $progressbar->update($count, $count, "Updating fileresponse feedback complete!");
            $toupdate->close();

        // Fileresponse savepoint reached.
        upgrade_plugin_savepoint(true, 2011102701, 'qtype', 'fileresponse');
    }

    if ($oldversion < 2011102702) {
        // Then we delete the old question_answers rows for fileresponse questions.
        $DB->delete_records_select('question_answers',
                "question IN (SELECT id FROM {question} WHERE qtype = 'fileresponse')");

        // Fileresponse savepoint reached.
        upgrade_plugin_savepoint(true, 2011102702, 'qtype', 'fileresponse');
    }

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this.

    if ($oldversion < 2013011800) {
        // Then we delete the old question_answers rows for fileresponse questions.
        $DB->delete_records_select('qtype_fileresponse_options', "NOT EXISTS (
                SELECT 1 FROM {question} WHERE qtype = 'fileresponse' AND
                    {question}.id = {qtype_fileresponse_options}.questionid)");

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2013011800, 'qtype', 'fileresponse');
    }

    if ($oldversion < 2013021700) {
        // Create new fields responsetemplate and responsetemplateformat in qtyep_essay_options table.
        $table = new xmldb_table('qtype_fileresponse_options');
        $field = new xmldb_field('responsetemplate', XMLDB_TYPE_TEXT, null, null,
                    null, null, null, 'graderinfoformat');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('responsetemplateformat', XMLDB_TYPE_INTEGER, '4',
                null, XMLDB_NOTNULL, null, '0', 'responsetemplate');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->execute("UPDATE {qtype_fileresponse_options} SET responsetemplate = '',
                responsetemplateformat = " . FORMAT_HTML . " WHERE responsetemplate IS NULL");

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2013021700, 'qtype', 'fileresponse');
    }

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

/**
 * Convert some content to HTML.
 * @param string $text the content to convert to HTML
 * @param int $oldformat One of the FORMAT_... constants.
 */
function qtype_fileresponse_convert_to_html($text, $oldformat) {
    switch ($oldformat) {
        // Similar to format_text.

        case FORMAT_PLAIN:
            $text = s($text);
            $text = str_replace(' ', '&nbsp; ', $text);
            $text = nl2br($text);
            return $text;

        case FORMAT_MARKDOWN:
            return markdown_to_html($text);

        case FORMAT_MOODLE:
            return text_to_html($text);

        case FORMAT_HTML:
            return $text;

        default:
            throw new coding_exception(
                    'Unexpected text format when upgrading fileresponse questions.');
    }
}
