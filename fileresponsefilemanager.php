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
 * Fileresponse FileManager form element
 *
 * Contains HTML class for a fileresponsefilemanager form element
 *
 * @package    qtype_fileresponse
 * @copyright  2022 Luca Bösch, BFH Bern University of Applied Sciences luca.boesch@bfh.ch
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once('HTML/QuickForm/element.php');
require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot . '/lib/form/templatable_form_element.php');

/**
 * Filemanager form element
 *
 * FileManager lets user to upload/manage multiple files
 *
 * @package    qtype_fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_fileresponsefilemanager extends HTML_QuickForm_element implements templatable {
    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /** @var string html for help button, if empty then no help will icon will be displayed. */
    public $_helpbutton = '';

    /** @var array options provided to initalize fileresponsefilemanager */
    // PHP doesn't support 'key' => $value1 | $value2 in class definition.
    // We cannot do $_options = array('return_types'=> FILE_INTERNAL | FILE_REFERENCE);
    // So I have to set null here, and do it in constructor.
    protected $_options = array('mainfile' => '', 'subdirs' => 1, 'maxbytes' => -1,
        'maxfiles' => -1, 'accepted_types' => '*', 'return_types' => null,
        'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED
    );

    /**
     * Constructor
     *
     * @param string $elementname (optional) name of the fileresponsefilemanager
     * @param string $elementlabel (optional) fileresponsefilemanager label
     * @param array $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     * @param array $options set of options to initalize fileresponsefilemanager
     */
    public function __construct($elementname = null, $elementlabel = null, $attributes = null, $options = null) {
        global $CFG, $PAGE;

        $options = (array) $options;
        foreach ($options as $name => $value) {
            if (array_key_exists($name, $this->_options)) {
                $this->_options[$name] = $value;
            }
        }
        if (!empty($options['maxbytes'])) {
            $this->_options['maxbytes'] = get_user_max_upload_file_size($PAGE->context,
                    $CFG->maxbytes, $options['maxbytes']);
        }
        if (empty($options['return_types'])) {
            $this->_options['return_types'] = (FILE_INTERNAL | FILE_REFERENCE | FILE_CONTROLLED_LINK);
        }
        $this->_type = 'fileresponsefilemanager';
        parent::__construct($elementname, $elementlabel, $attributes);
    }

    // @codingStandardsIgnoreStart

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    public function onQuickFormEvent($event, $arg, &$caller) {
        switch ($event) {
            case 'createElement':
                $caller->setType($arg[0], PARAM_INT);
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    // @codingStandardsIgnoreEnd

    /**
     * Sets name of fileresponsefilemanager
     *
     * @param string $name name of the fileresponsefilemanager
     */
    public function setname($name) {
        $this->updateAttributes(array('name' => $name));
    }

    /**
     * Returns name of fileresponsefilemanager
     *
     * @return string
     */
    public function getname() {
        return $this->getAttribute('name');
    }

    /**
     * Updates fileresponsefilemanager attribute value
     *
     * @param string $value value to set
     */
    public function setvalue($value) {
        $this->updateAttributes(array('value' => $value));
    }

    /**
     * Returns fileresponsefilemanager attribute value
     *
     * @return string
     */
    public function getvalue() {
        return $this->getAttribute('value');
    }

    /**
     * Returns maximum file size which can be uploaded
     *
     * @return int
     */
    public function getmaxbytes() {
        return $this->_options['maxbytes'];
    }

    /**
     * Sets maximum file size which can be uploaded
     *
     * @param int $maxbytes file size
     */
    public function setmaxbytes($maxbytes) {
        global $CFG, $PAGE;
        $this->_options['maxbytes'] = get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes,
                $maxbytes);
    }

    /**
     * Returns the maximum size of the area.
     *
     * @return int
     */
    public function getareamaxbytes() {
        return $this->_options['areamaxbytes'];
    }

    /**
     * Sets the maximum size of the area.
     *
     * @param int $areamaxbytes size limit
     */
    public function setareamaxbytes($areamaxbytes) {
        $this->_options['areamaxbytes'] = $areamaxbytes;
    }

    /**
     * Returns true if subdirectoy can be created, else false
     *
     * @return bool
     */
    public function getsubdirs() {
        return $this->_options['subdirs'];
    }

    /**
     * Set option to create sub directory, while uploading file
     *
     * @param bool $allow true if sub directory can be created.
     */
    public function setsubdirs($allow) {
        $this->_options['subdirs'] = $allow;
    }

    /**
     * Returns maximum number of files which can be uploaded
     *
     * @return int
     */
    public function getmaxfiles() {
        return $this->_options['maxfiles'];
    }

    /**
     * Sets maximum number of files which can be uploaded.
     *
     * @param int $num number of files
     */
    public function setmaxfiles($num) {
        $this->_options['maxfiles'] = $num;
    }

    /**
     * Returns html for help button.
     *
     * @return string html for help button
     */
    public function gethelpbutton() {
        return $this->_helpbutton;
    }

    /**
     * Returns type of fileresponsefilemanager element
     *
     * @return string
     */
    public function getelementtemplatetype() {
        if ($this->_flagFrozen) {
            return 'nodisplay';
        } else {
            return 'default';
        }
    }

    /**
     * Returns HTML for fileresponsefilemanager form element.
     *
     * @return string
     */
    public function tohtml() {
        global $CFG, $USER, $COURSE, $PAGE, $OUTPUT;
        require_once("$CFG->dirroot/repository/lib.php");

        // Security - never ever allow guest/not logged in user to upload anything or use this
        // element!
        if (isguestuser() or !isloggedin()) {
            throw new moodle_exception('noguest');
        }

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $id = $this->_attributes['id'];
        $elname = $this->_attributes['name'];
        $subdirs = $this->_options['subdirs'];
        $maxbytes = $this->_options['maxbytes'];
        $draftitemid = $this->getvalue();
        $acceptedtypes = $this->_options['accepted_types'];

        if (empty($draftitemid)) {
            // No existing area info provided - let's use fresh new draft area.
            require_once("$CFG->libdir/filelib.php");
            $this->setvalue(file_get_unused_draft_itemid());
            $draftitemid = $this->getvalue();
        }

        $clientid = uniqid();

        // Fileresponsefilemanager options.
        $options = new stdClass();
        $options->mainfile = $this->_options['mainfile'];
        $options->maxbytes = $this->_options['maxbytes'];
        $options->maxfiles = $this->getmaxfiles();
        $options->client_id = $clientid;
        $options->itemid = $draftitemid;
        $options->subdirs = $this->_options['subdirs'];
        $options->target = $id;
        $options->accepted_types = $acceptedtypes;
        $options->return_types = $this->_options['return_types'];
        $options->context = $PAGE->context;
        $options->areamaxbytes = $this->_options['areamaxbytes'];

        $html = $this->_getTabs();
        $fm = new form_fileresponsefilemanager($options);
        $output = $PAGE->get_renderer('core', 'files');
        $html .= $output->render($fm);

        $html .= html_writer::empty_tag('input',
                array('value' => $draftitemid, 'name' => $elname, 'type' => 'hidden'
                ));
        // Label element needs 'for' attribute work.
        $html .= html_writer::empty_tag('input',
                array('value' => '', 'id' => 'id_' . $elname, 'type' => 'hidden'
                ));

        if (!empty($options->accepted_types) && $options->accepted_types != '*') {
            $html .= html_writer::tag('p', get_string('filesofthesetypes', 'form'));
            $util = new \core_form\filetypes_util();
            $filetypes = $options->accepted_types;
            $filetypedescriptions = $util->describe_file_types($filetypes);
            $html .= $OUTPUT->render_from_template('core_form/filetypes-descriptions', $filetypedescriptions);
        }

        return $html;
    }

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);
        $context['html'] = $this->tohtml();
        return $context;
    }

    // @codingStandardsIgnoreStart

    /**
     * Check that all files have the allowed type.
     *
     * @param int $value Draft item id with the uploaded files.
     * @return string|null Validation error message or null.
     */
    public function validateSubmitValue($value) {

        if (empty($value)) {
            return;
        }

        $filetypesutil = new \core_form\filetypes_util();
        $allowlist = $filetypesutil->normalize_file_types($this->_options['accepted_types']);

        if (empty($allowlist) || $allowlist === ['*']) {
            // Any file type is allowed, nothing to check here.
            return;
        }

        $draftfiles = file_get_all_files_in_draftarea($value);
        $wrongfiles = array();

        if (empty($draftfiles)) {
            // No file uploaded, nothing to check here.
            return;
        }

        foreach ($draftfiles as $file) {
            if (!$filetypesutil->is_allowed_file_type($file->filename, $allowlist)) {
                $wrongfiles[] = $file->filename;
            }
        }

        if ($wrongfiles) {
            $a = array(
                'allowlist' => implode(', ', $allowlist),
                'wrongfiles' => implode(', ', $wrongfiles),
            );
            return get_string('err_wrongfileextension', 'core_form', $a);
        }

        return;
    }
}

// @codingStandardsIgnoreEnd

/**
 * Data structure representing a fileresponse file manager.
 *
 * This class defines the data structure for a fileresponse file manager
 *
 * Layout things are mainly taken from theme/boost/templates/core/filemanager_modal_generallayout.mustache.
 *
 * @package    qtype_fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @todo do not use this abstraction (skodak)
 */
class form_fileresponsefilemanager implements renderable {

    /** @var stdClass $options options for fileresponsefilemanager */
    public $options;

    /**
     * Constructor
     *
     * @param stdClass $options options for fileresponsefilemanager
     *        default options are:
     *        maxbytes=>-1,
     *        areamaxbytes => FILE_AREA_MAX_BYTES_UNLIMITED,
     *        maxfiles=>-1,
     *        itemid=>0,
     *        subdirs=>false,
     *        client_id=>uniqid(),
     *        acepted_types=>'*',
     *        return_types=>FILE_INTERNAL,
     *        context=>$PAGE->context,
     *        author=>fullname($USER),
     *        licenses=>array build from $CFG->licenses,
     *        defaultlicense=>$CFG->sitedefaultlicense
     */
    public function __construct(stdClass $options) {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot . '/repository/lib.php');
        $defaults = array('maxbytes' => -1, 'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
            'maxfiles' => -1, 'itemid' => 0, 'subdirs' => 0, 'client_id' => uniqid(),
            'accepted_types' => '*', 'return_types' => FILE_INTERNAL, 'context' => $PAGE->context,
            'author' => fullname($USER), 'licenses' => array()
        );
        if (!empty($CFG->licenses)) {
            $array = explode(',', $CFG->licenses);
            foreach ($array as $license) {
                $l = new stdClass();
                $l->shortname = $license;
                $l->fullname = get_string($license, 'license');
                $defaults['licenses'][] = $l;
            }
        }
        if (!empty($CFG->sitedefaultlicense)) {
            $defaults['defaultlicense'] = $CFG->sitedefaultlicense;
        }
        foreach ($defaults as $key => $value) {
            // Using !isset() prevents us from overwriting falsey values with defaults (as empty()
            // did).
            if (!isset($options->$key)) {
                $options->$key = $value;
            }
        }

        $fs = get_file_storage();

        // Initialise options, getting files in root path.
        $this->options = file_get_drafarea_files($options->itemid, '/');

        // Calculate file count.
        $usercontext = context_user::instance($USER->id);
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $options->itemid, 'id',
                false);
        $filecount = count($files);
        $this->options->filecount = $filecount;

        // Copying other options.
        foreach ($options as $name => $value) {
            $this->options->$name = $value;
        }

        // Calculate the maximum file size as minimum from what is specified in filepicker options,
        // course options, global configuration and php settings.
        $coursebytes = $maxbytes = 0;
        list($context, $course, $cm) = get_context_info_array($this->options->context->id);
        if (is_object($course)) {
            $coursebytes = $course->maxbytes;
        }
        if (!empty($this->options->maxbytes) && $this->options->maxbytes > 0) {
            $maxbytes = $this->options->maxbytes;
        }
        $this->options->maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes,
                $coursebytes, $maxbytes);

        $this->options->userprefs = array();
        $this->options->userprefs['recentviewmode'] = get_user_preferences('filemanager_recentviewmode', '');
        user_preference_allow_ajax_update('filemanager_recentviewmode', PARAM_INT);

        // Building file picker options.
        $params = new stdClass();
        $params->accepted_types = $options->accepted_types;
        $params->return_types = $options->return_types;
        $params->context = $options->context;
        $params->env = 'fileresponsefilemanager';
        $params->disable_types = !empty($options->disable_types) ? $options->disable_types : array();
        $filepickeroptions = initialise_filepicker($params);
        // If filepicker plugins aren't allowed make sure that only upload repository is available
        // for students.
        if (!$options->allowpickerplugins) {
            foreach ($filepickeroptions->repositories as $repository) {
                if ($repository->type !== 'upload') {
                    unset($filepickeroptions->repositories[$repository->id]);
                } else {
                    $filepickeroptions->userprefs['recentrepository'] = $repository->id; // Make it
                                                                                              // active
                                                                                              // tab!
                }
            }
        }
        $this->options->filepicker = $filepickeroptions;
    }

    public function get_nonjsurl() {
        global $PAGE;
        return new moodle_url('/repository/draftfiles_manager.php',
            array('env' => 'fileresponsefilemanager',
                'action' => 'browse',
                'itemid' => $this->options->itemid,
                'subdirs' => $this->options->subdirs,
                'maxbytes' => $this->options->maxbytes,
                'areamaxbytes' => $this->options->areamaxbytes,
                'maxfiles' => $this->options->maxfiles,
                'ctx_id' => $PAGE->context->id,  // TODO ?
                'course' => $PAGE->course->id, // TODO ?
                'sesskey' => sesskey()
            )
        );
    }
}


class qtype_fileresponse_fileresponsefilemanager_renderer extends plugin_renderer_base {

    /**
     * Prints the file manager and initializes all necessary libraries
     * This is mainly taken from files/renderer.php.
     *
     * <pre>
     * $fm = new form_fileresponsefilemanager($options);
     * $output = get_renderer('core', 'files');
     * echo $output->render($fm);
     * </pre>
     *
     * @param form_fileresponsefilemanager $fm File manager to render
     * @return string HTML fragment
     */
    public function render_form_fileresponsefilemanager($fm) {
        $html = $this->fm_print_generallayout($fm);
        $module = array(
            'name' => 'form_fileresponsefilemanager',
            'fullpath' => '/question/type/fileresponse/fileresponsefilemanager.js',
            'requires' => array('moodle-core-notification-dialogue', 'core_filepicker', 'base', 'io-base', 'node', 'json',
                'core_dndupload', 'panel', 'resize-plugin', 'dd-plugin'),
            'strings' => array(
                array('error', 'moodle'), array('info', 'moodle'), array('confirmdeletefile', 'repository'),
                array('draftareanofiles', 'repository'), array('entername', 'repository'), array('enternewname', 'repository'),
                array('invalidjson', 'repository'), array('popupblockeddownload', 'repository'),
                array('unknownoriginal', 'repository'), array('confirmdeletefolder', 'repository'),
                array('confirmdeletefilewithhref', 'repository'), array('confirmrenamefolder', 'repository'),
                array('confirmrenamefile', 'repository'), array('newfolder', 'repository'), array('edit', 'moodle')
            )
        );
        $this->page->requires->js_init_call('M.form_fileresponsefilemanager.set_templates',
                array($this->fileresponsefilemanager_js_templates()), true, $module);
        $this->page->requires->js_init_call('M.form_fileresponsefilemanager.init', array($fm->options), true, $module);

        // Non javascript file manager.
        $html .= '<noscript>';
        $html .= "<div><object type='text/html' data='" . $fm->get_nonjsurl() .
            "' height='160' width='600' style='border:1px solid #000'></object></div>";
        $html .= '</noscript>';

        return $html;
    }


    /**
     * Returns html for displaying one file manager
     *
     * The main element in HTML must have id="fileresponsefilemanager-{$client_id}" and
     * class="fileresponsefilemanager fm-loading";
     * After all necessary code on the page (both html and javascript) is loaded,
     * the class fm-loading will be removed and added class fm-loaded;
     * The main element (class=fileresponsefilemanager) will be assigned the following classes:
     * 'fm-maxfiles' - when fileresponsefilemanager has maximum allowed number of files;
     * 'fm-nofiles' - when fileresponsefilemanager has no files at all (although there might be
     * folders);
     * 'fm-noitems' - when current view (folder) has no items - neither files nor folders;
     * 'fm-updating' - when current view is being updated (usually means that loading icon is to be
     * displayed);
     * 'fm-nomkdir' - when 'Make folder' action is unavailable (empty($fm->options->subdirs) ==
     * true)
     *
     * Element with class 'fileresponsefilemanager-container' will be holding evens for dnd upload
     * (dragover, etc.).
     * It will have class:
     * 'dndupload-ready' - when a file is being dragged over the browser
     * 'dndupload-over' - when file is being dragged over this filepicker (additional to
     * 'dndupload-ready')
     * 'dndupload-uploading' - during the upload process (note that after dnd upload process is
     * over, the file manager will refresh the files list and therefore will have for a while class
     * fm-updating. Both waiting processes should look similar so the images don't jump for user)
     *
     * If browser supports Drag-and-drop, the body element will have class 'dndsupported',
     * otherwise - 'dndnotsupported';
     *
     * Element with class 'fp-content' will be populated with files list;
     * Element with class 'fp-btn-add' will hold onclick event for adding a file (opening
     * filepicker);
     * Element with class 'fp-btn-mkdir' will hold onclick event for adding new folder;
     * Element with class 'fp-btn-download' will hold onclick event for download action;
     *
     * Element with class 'fp-path-folder' is a template for one folder in path toolbar.
     * It will hold mouse click event and will be assigned classes first/last/even/odd respectfully.
     * Parent element will receive class 'empty' when there are no folders to be displayed;
     * The content of subelement with class 'fp-path-folder-name' will be substituted with folder
     * name;
     *
     * Element with class 'fp-viewbar' will have the class 'enabled' or 'disabled' when view mode
     * can be changed or not;
     * Inside element with class 'fp-viewbar' there are expected elements with classes
     * 'fp-vb-icons', 'fp-vb-tree' and 'fp-vb-details'. They will handle onclick events to switch
     * between the view modes, the last clicked element will have the class 'checked';
     *
     * @param form_fileresponsefilemanager $fm
     * @return string
     */
    protected function fm_print_generallayout($fm) {
        $options = $fm->options;
        $clientid = $options->client_id;
        $straddfile = get_string('addfile', 'repository');
        $strmakedir = get_string('makeafolder', 'moodle');
        $strdownload = get_string('downloadfolder', 'repository');
        $strloading = get_string('loading', 'repository');
        $strdroptoupload = get_string('droptoupload', 'moodle');
        $iconprogress = $this->output->pix_icon('i/loading_small', $strloading) . '';
        $restrictions = $this->fm_print_restrictions($fm);
        $strdndnotsupported = get_string('dndnotsupported_insentence', 'moodle') .
            $this->output->help_icon('dndnotsupported');
        $strdndenabledinbox = get_string('dndenabled_inbox', 'moodle');
        $loading = get_string('loading', 'repository');
        $straddfiletext = get_string('addfiletext', 'repository');
        $strcreatefolder = get_string('createfolder', 'repository');
        $strdownloadallfiles = get_string('downloadallfiles', 'repository');

        $html = '
<div id="fileresponsefilemanager-' . $clientid . '" class="filemanager fm-loading">
    <div class="fp-restrictions">
        ' . $restrictions . '
        <span class="dnduploadnotsupported-message"> - ' .
                     $strdndnotsupported . ' </span>
    </div>
    <div class="fp-navbar bg-faded card mb-0 clearfix icon-no-spacing">
        <div class="filemanager-toolbar icon-no-spacing">
            <div class="fp-toolbar">
                <div class="fp-btn-add">
                    <a role="button" title="' . $straddfile . '" class="btn btn-secondary btn-sm" href="#">
                        ' . $this->pix_icon('a/add_file', $straddfiletext) . '
                    </a>
                </div>
                <div class="fp-btn-mkdir">
                    <a role="button" title="' . $strmakedir . '" class="btn btn-secondary btn-sm" href="#">
                        ' . $this->pix_icon('a/create_folder', $strcreatefolder) . '
                    </a>
                </div>
                <div class="fp-btn-download">
                    <a role="button" title="' . $strdownload . '" class="btn btn-secondary btn-sm" href="#">
                        ' . $this->pix_icon('a/download_all', $strdownloadallfiles) . '
                    </a>
                </div>
                <span class="fp-img-downloading">
                    <span class="sr-only">' . get_string('loadinghelp') . '</span>
                    ' . $this->pix_icon('i/loading_small', '') . '
                </span>
            </div>
            <div class="fp-viewbar btn-group float-sm-right">
                <a role="button" title="'. get_string('displayicons', 'repository') .
                    '" class="fp-vb-icons btn btn-secondary btn-sm" href="#">' .
                    $this->pix_icon('fp/view_icon_active', get_string('displayasicons', 'repository'), 'theme') . '
                </a>
                <a role="button" title="'. get_string('displaydetails', 'repository') .
                    '" class="fp-vb-details btn btn-secondary btn-sm" href="#">' .
                    $this->pix_icon('fp/view_list_active', get_string('displayasdetails', 'repository'), 'theme') . '
                </a>
                <a role="button" title="'. get_string('displaytree', 'repository') .
                    '" class="fp-vb-tree btn btn-secondary btn-sm" href="#">' .
                    $this->pix_icon('fp/view_tree_active', get_string('displayastree', 'repository'), 'theme') . '
                </a>
            </div>
        </div>
        <div class="fp-pathbar">
            <span class="fp-path-folder"><a class="fp-path-folder-name" href="#"></a></span>
        </div>
    </div>
    <div class="filemanager-loading mdl-align">'.$iconprogress.'</div>
    <div class="filemanager-container card" >
        <div class="fm-content-wrapper">
            <div class="fp-content"></div>
            <div class="fm-empty-container">
                <div class="dndupload-message">' .
                     $strdndenabledinbox .
                 '<br/><div class="dndupload-arrow"></div></div>
            </div>
            <div class="dndupload-target">' .
                 $strdroptoupload . '<br/><div class="dndupload-arrow"></div></div>
            <div class="dndupload-progressbars"></div>
            <div class="dndupload-uploadinprogress">'.$iconprogress.'</div>
        </div>
        <div class="filemanager-updating">'.$iconprogress.'</div>
    </div>
</div>';
        return $html;
    }

    /**
     * fileresponsefilemanager JS template for displaying one file in 'icon view' mode.
     *
     * Except for elements described in fp_js_template_iconfilename, this template may also
     * contain element with class 'fp-contextmenu'. If context menu is available for this
     * file, the top element will receive the additional class 'fp-hascontextmenu' and
     * the element with class 'fp-contextmenu' will hold onclick event for displaying
     * the context menu.
     *
     * @see fp_js_template_iconfilename()
     * @return string
     */
    protected function fm_js_template_iconfilename() {
        $rv = '
<div class="fp-file">
    <a href="#">
    <div style="position:relative;">
        <div class="fp-thumbnail"></div>
        <div class="fp-reficons1"></div>
        <div class="fp-reficons2"></div>
    </div>
    <div class="fp-filename-field">
        <div class="fp-filename"></div>
    </div>
    </a>
    <a class="fp-contextmenu" href="#">' .
                 $this->pix_icon('i/menu', '▶') . '</a>
</div>';
        return $rv;
    }

    /**
     * fileresponsefilemanager JS template for displaying file name in 'table view' and 'tree view'
     * modes.
     *
     * Except for elements described in fp_js_template_listfilename, this template may also
     * contain element with class 'fp-contextmenu'. If context menu is available for this
     * file, the top element will receive the additional class 'fp-hascontextmenu' and
     * the element with class 'fp-contextmenu' will hold onclick event for displaying
     * the context menu.
     *
     * @todo MDL-32736 remove onclick="return false;"
     * @see fp_js_template_listfilename()
     * @return string
     */
    protected function fm_js_template_listfilename() {
        $rv = '
<span class="fp-filename-icon">
    <a href="#">
    <span class="fp-icon"></span>
    <span class="fp-reficons1"></span>
    <span class="fp-reficons2"></span>
    <span class="fp-filename"></span>
    </a>
    <a class="fp-contextmenu" href="#" onclick="return false;">' .
                 $this->pix_icon('i/menu', '▶') . '</a>
</span>';
        return $rv;
    }

    /**
     * fileresponsefilemanager JS template for displaying 'Make new folder' dialog.
     *
     * Must be wrapped in an element, CSS for this element must define width and height of the
     * window;
     *
     * Must have one input element with type="text" (for users to enter the new folder name);
     *
     * content of element with class 'fp-dlg-curpath' will be replaced with current path where
     * new folder is about to be created;
     * elements with classes 'fp-dlg-butcreate' and 'fp-dlg-butcancel' will hold onclick events;
     *
     * @return string
     */
    protected function fm_js_template_mkdir() {
        $rv = '
<div class="filemanager fp-mkdir-dlg" role="dialog" aria-live="assertive" aria-labelledby="fp-mkdir-dlg-title">
    <div class="fp-mkdir-dlg-text">
        <label id="fp-mkdir-dlg-title">' .
                 get_string('newfoldername', 'repository') . '</label><br/>
        <input type="text" />
    </div>
    <button class="fp-dlg-butcreate btn-primary btn">' .
                 get_string('makeafolder') . '</button>
    <button class="fp-dlg-butcancel btn-cancel btn">' .
                 get_string('cancel') . '</button>
</div>';
        return $rv;
    }

    /**
     * fileresponsefilemanager JS template for error/info message displayed as a separate popup
     * window.
     *
     * @see fp_js_template_message()
     * @return string
     */
    protected function fm_js_template_message() {
        return $this->fp_js_template_message();
    }

    /**
     * fileresponsefilemanager JS template for window with file information/actions.
     *
     * Styles are mainly taken from theme/boost/templates/core/filemanager_fileselect.mustache.
     *
     * All content must be enclosed in one element, CSS for this class must define width and
     * height of the window;
     *
     * Thumbnail image will be added as content to the element with class 'fp-thumbnail';
     *
     * Inside the window the elements with the following classnames must be present:
     * 'fp-saveas', 'fp-author', 'fp-license', 'fp-path'. Inside each of them must be
     * one input element (or select in case of fp-license and fp-path). They may also have labels.
     * The elements will be assign with class 'uneditable' and input/select element will become
     * disabled if they are not applicable for the particular file;
     *
     * There may be present elements with classes 'fp-original', 'fp-datemodified',
     * 'fp-datecreated',
     * 'fp-size', 'fp-dimensions', 'fp-reflist'. They will receive additional class 'fp-unknown' if
     * information is unavailable. If there is information available, the content of embedded
     * element with class 'fp-value' will be substituted with the value;
     *
     * The value of Original ('fp-original') is loaded in separate request. When it is applicable
     * but not yet loaded the 'fp-original' element receives additional class 'fp-loading';
     *
     * The value of 'Aliases/Shortcuts' ('fp-reflist') is also loaded in separate request. When it
     * is applicable but not yet loaded the 'fp-original' element receives additional class
     * 'fp-loading'. The string explaining that XX references exist will replace content of element
     * 'fp-refcount'. Inside '.fp-reflist .fp-value' each reference will be enclosed in <li>;
     *
     * Elements with classes 'fp-file-update', 'fp-file-download', 'fp-file-delete', 'fp-file-zip',
     * 'fp-file-unzip', 'fp-file-setmain' and 'fp-file-cancel' will hold corresponding onclick
     * events (there may be several elements with class 'fp-file-cancel');
     *
     * When confirm button is pressed and file is being selected, the top element receives
     * additional class 'loading'. It is removed when response from server is received.
     *
     * When any of the input fields is changed, the top element receives class 'fp-changed';
     * When current file can be set as main - top element receives class 'fp-cansetmain';
     * When current file is folder/zip/file - top element receives respectfully class
     * 'fp-folder'/'fp-zip'/'fp-file';
     *
     * @return string
     */
    protected function fm_js_template_fileselectlayout() {
        $strloading = get_string('loading', 'repository');
        $iconprogress = $this->pix_icon('i/loading_small', $strloading) . '';
        $rv = '
<div class="filemanager fp-select">
    <div class="fp-select-loading">
        ' . $this->pix_icon('i/loading_small', '') . '
    </div>
    <form class="mform clearfix">
        <div class="form-group mx-0">
            <button class="fp-file-download btn btn-secondary">' . get_string('download') . '</button>
            <button class="fp-file-delete btn btn-secondary">' . get_string('delete') . '</button>
            <button class="fp-file-setmain btn btn-secondary">' .
                 get_string('setmainfile', 'repository') .
                 '</button>
            <span class="fp-file-setmain-help">' .
                 $this->output->help_icon('setmainfile', 'repository') . '</span>
            <button class="fp-file-zip btn btn-secondary">' .
                 get_string('zip', 'editor') . '</button>
            <button class="fp-file-unzip btn btn-secondary">' . get_string('unzip') . '</button>
        </div>

            <div class="fp-saveas form-group row mx-0">
                <label class="form-control-label col-4 px-0">' .
                 get_string('name', 'repository') . '</label>
                <label class="col-8 form-inline">
                    <input class="form-control" type="text"/>
                </label>
            </div>
            <div class="fp-author form-group row mx-0">
                <div class="form-control-label col-4 px-0">' .
                 get_string('author', 'repository') .
                 '</div>
                <div class="col-8 form-inline">
                    <input class="form-control" type="text"/>
                </div>
            </div>
            <div class="fp-license form-group row mx-0">
                <div class="form-control-label col-4 px-0">' .
                 get_string('chooselicense', 'repository') . '</div>
                <div class="col-8 form-inline pr-0">
                    <select class="custom-select form-control"></select>
                </div>
            </div>
            <div class="fp-path form-group row mx-0">
                <label class="form-control-label col-4 px-0">' .
                 get_string('path', 'repository') . '</label>
                <div class="col-8 form-inline pr-0">
                    <select class="custom-select form-control"></select>
                </div>
            </div>
            <div class="fp-original control-group clearfix">
                <div class="form-control-label col-4 px-0">' .
                 get_string('original', 'repository') .
                 '</div>
                <div class="col-8 form-inline">
                    <span class="fp-originloading">' .
                 $iconprogress . ' ' . $strloading .
                 '</span><span class="fp-value"></span>
                </div>
            </div>
            <div class="fp-reflist control-group clearfix">
                <div class="form-control-label col-4 px-0">' .
                 get_string('referenceslist', 'repository') .
                 '</div>
                <div class="col-8 form-inline">
                    <p class="fp-refcount"></p>
                    <span class="fp-reflistloading">' .
                 $iconprogress . ' ' . $strloading .
                 '</span>
                    <ul class="fp-value"></ul>
                </div>
        </div>
        <div class="fp-select-buttons form-group">
            <button class="fp-file-update btn-primary btn">' .
                 get_string('update', 'moodle') . '</button>
            <button class="fp-file-cancel btn-cancel btn">' .
                 get_string('cancel') .
                 '</button>
        </div>
    </form>
    <div class="fp-info clearfix">
        <div class="fp-hr"></div>
        <p class="fp-thumbnail"></p>
        <div class="fp-fileinfo">
            <div class="fp-datemodified">' .
                 get_string('lastmodified', 'repository') .
                 ' <span class="fp-value"></span></div>
            <div class="fp-datecreated">' .
                 get_string('datecreated', 'repository') .
                 ' <span class="fp-value"></span></div>
            <div class="fp-size">' .
                 get_string('size', 'repository') .
                 ' <span class="fp-value"></span></div>
            <div class="fp-dimensions">' .
                 get_string('dimensions', 'repository') . ' <span class="fp-value"></span></div>
        </div>
    </div>
</div>';
        return $rv;
    }

    /**
     * fileresponsefilemanager JS template for popup confirm dialogue window.
     *
     * Must have one top element, CSS for this element must define width and height of the window;
     *
     * content of element with class 'fp-dlg-text' will be replaced with dialog text;
     * elements with classes 'fp-dlg-butconfirm' and 'fp-dlg-butcancel' will
     * hold onclick events;
     *
     * @return string
     */
    protected function fm_js_template_confirmdialog() {
        $rv = '
<div class="filemanager fp-dlg">
    <div class="fp-dlg-text"></div>
    <button class="fp-dlg-butconfirm btn-primary btn">' . get_string('ok') . '</button>
    <button class="fp-dlg-butcancel btn-cancel btn">' .
                 get_string('cancel') . '</button>
</div>';
        return $rv;
    }

    /**
     * Returns all fileresponsefilemanager JavaScript templates as an array.
     *
     * @return array
     */
    public function fileresponsefilemanager_js_templates() {
        $classmethods = get_class_methods($this);
        $templates = array();
        foreach ($classmethods as $methodname) {
            if (preg_match('/^fm_js_template_(.*)$/', $methodname, $matches)) {
                $templates[$matches[1]] = $this->$methodname();
            }
        }
        return $templates;
    }

    /**
     * Displays restrictions for the file manager
     *
     * @param form_fileresponsefilemanager $fm
     * @return string
     */
    protected function fm_print_restrictions($fm) {
        $maxbytes = display_size($fm->options->maxbytes);
        $strparam = (object) array('size' => $maxbytes, 'attachments' => $fm->options->maxfiles,
            'areasize' => display_size($fm->options->areamaxbytes)
        );
        $hasmaxfiles = !empty($fm->options->maxfiles) && $fm->options->maxfiles > 0;
        $hasarealimit = !empty($fm->options->areamaxbytes) && $fm->options->areamaxbytes != -1;
        if ($hasmaxfiles && $hasarealimit) {
            $maxsize = get_string('maxsizeandattachmentsandareasize', 'moodle', $strparam);
        } else if ($hasmaxfiles) {
            $maxsize = get_string('maxsizeandattachments', 'moodle', $strparam);
        } else if ($hasarealimit) {
            $maxsize = get_string('maxsizeandareasize', 'moodle', $strparam);
        } else {
            $maxsize = get_string('maxfilesize', 'moodle', $maxbytes);
        }
        // TODO MDL-32020 also should say about 'File types accepted'.
        return '<span>' . $maxsize . '</span>';
    }

    /**
     * Template for FilePicker with general layout (not QuickUpload).
     *
     * Must have one top element containing everything else (recommended <div class="file-picker">),
     * CSS for this element must define width and height of the filepicker window. Or CSS must
     * define min-width, max-width, min-height and max-height and in this case the filepicker
     * window will be resizeable;
     *
     * Element with class 'fp-viewbar' will have the class 'enabled' or 'disabled' when view mode
     * can be changed or not;
     * Inside element with class 'fp-viewbar' there are expected elements with classes
     * 'fp-vb-icons', 'fp-vb-tree' and 'fp-vb-details'. They will handle onclick events to switch
     * between the view modes, the last clicked element will have the class 'checked';
     *
     * Element with class 'fp-repo' is a template for displaying one repository. Other repositories
     * will be attached as siblings (classes first/last/even/odd will be added respectfully).
     * The currently selected repostory will have class 'active'. Contents of element with class
     * 'fp-repo-name' will be replaced with repository name, source of image with class
     * 'fp-repo-icon' will be replaced with repository icon;
     *
     * Element with class 'fp-content' is obligatory and will hold the current contents;
     *
     * Element with class 'fp-paging' will contain page navigation (will be deprecated soon);
     *
     * Element with class 'fp-path-folder' is a template for one folder in path toolbar.
     * It will hold mouse click event and will be assigned classes first/last/even/odd respectfully.
     * Parent element will receive class 'empty' when there are no folders to be displayed;
     * The content of subelement with class 'fp-path-folder-name' will be substituted with folder
     * name;
     *
     * Element with class 'fp-toolbar' will have class 'empty' if all 'Back', 'Search', 'Refresh',
     * 'Logout', 'Manage' and 'Help' are unavailable for this repo;
     *
     * Inside fp-toolbar there are expected elements with classes fp-tb-back, fp-tb-search,
     * fp-tb-refresh, fp-tb-logout, fp-tb-manage and fp-tb-help. Each of them will have
     * class 'enabled' or 'disabled' if particular repository has this functionality.
     * Element with class 'fp-tb-search' must contain empty form inside, it's contents will
     * be substituted with the search form returned by repository (in the most cases it
     * is generated with template core_repository_renderer::repository_default_searchform);
     * Other elements must have either <a> or <button> element inside, it will hold onclick
     * event for corresponding action; labels for fp-tb-back and fp-tb-logout may be
     * replaced with those specified by repository;
     *
     * @return string
     */
    protected function fp_js_template_generallayout() {
        $rv = '
<div tabindex="0" class="file-picker fp-generallayout" role="dialog" aria-live="assertive">
    <div class="fp-repo-area">
        <ul class="fp-list" role="tablist">
            <li class="fp-repo" role="tab" aria-selected="false" tabindex="-1">
                <a href="#" tabindex="-1"><img class="fp-repo-icon" alt=" " width="16" height="16" />&nbsp;
                <span class="fp-repo-name"></span></a>
            </li>
        </ul>
    </div>
    <div class="fp-repo-items" tabindex="0">
        <div class="fp-navbar">
            <div>
                <div class="fp-toolbar">
                    <div class="fp-tb-back">
                        <a href="#">'.get_string('back', 'repository').'</a>
                    </div>
                    <div class="fp-tb-search">
                        <form></form>
                    </div>
                    <div class="fp-tb-refresh">
                        <a title="'. get_string('refresh', 'repository') .'" href="#">
                            ' . $this->pix_icon('a/refresh', '') . '
                        </a>
                    </div>
                    <div class="fp-tb-logout">
                        <a title="'. get_string('logout', 'repository') .'" href="#">
                            ' . $this->pix_icon('a/logout', '') . '
                        </a>
                    </div>
                    <div class="fp-tb-manage">
                        <a title="'. get_string('manageurl', 'repository') .'" href="#">
                            ' . $this->pix_icon('a/setting', '') . '
                        </a>
                    </div>
                    <div class="fp-tb-help">
                        <a title="'. get_string('help', 'repository') .'" href="#">
                            ' . $this->pix_icon('a/help', '') . '
                        </a>
                    </div>
                    <div class="fp-tb-message"></div>
                </div>
                <div class="fp-viewbar">
                    <a title="'. get_string('displayicons', 'repository') .'" class="fp-vb-icons" href="#">
                        ' . $this->pix_icon('fp/view_icon_active', '', 'theme') . '
                    </a>
                    <a title="'. get_string('displaydetails', 'repository') .'" class="fp-vb-details" href="#">
                        ' . $this->pix_icon('fp/view_list_active', '', 'theme') . '
                    </a>
                    <a title="'. get_string('displaytree', 'repository') .'" class="fp-vb-tree" href="#">
                        ' . $this->pix_icon('fp/view_tree_active', '', 'theme') . '
                    </a>
                </div>
                <div class="fp-clear-left"></div>
            </div>
            <div class="fp-pathbar">
                 <span class="fp-path-folder"><a class="fp-path-folder-name" href="#"></a></span>
            </div>
        </div>
        <div class="fp-content"></div>
    </div>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for displaying one file in 'icon view' mode.
     *
     * the element with class 'fp-thumbnail' will be resized to the repository thumbnail size
     * (both width and height, unless min-width and/or min-height is set in CSS) and the content of
     * an element will be replaced with an appropriate img;
     *
     * the width of element with class 'fp-filename' will be set to the repository thumbnail width
     * (unless min-width is set in css) and the content of an element will be replaced with filename
     * supplied by repository;
     *
     * top element(s) will have class fp-folder if the element is a folder;
     *
     * List of files will have parent <div> element with class 'fp-iconview'
     *
     * @return string
     */
    protected function fp_js_template_iconfilename() {
        $rv = '
<a class="fp-file" href="#" >
    <div style="position:relative;">
        <div class="fp-thumbnail"></div>
        <div class="fp-reficons1"></div>
        <div class="fp-reficons2"></div>
    </div>
    <div class="fp-filename-field">
        <p class="fp-filename"></p>
    </div>
</a>';
        return $rv;
    }

    /**
     * FilePicker JS template for displaying file name in 'table view' and 'tree view' modes.
     *
     * content of the element with class 'fp-icon' will be replaced with an appropriate img;
     *
     * content of element with class 'fp-filename' will be replaced with filename supplied by
     * repository;
     *
     * top element(s) will have class fp-folder if the element is a folder;
     *
     * Note that tree view and table view are the YUI widgets and therefore there are no
     * other templates. The widgets will be wrapped in <div> with class fp-treeview or
     * fp-tableview (respectfully).
     *
     * @return string
     */
    protected function fp_js_template_listfilename() {
        $rv = '
<span class="fp-filename-icon">
    <a href="#">
        <span class="fp-icon"></span>
        <span class="fp-filename"></span>
    </a>
</span>';
        return $rv;
    }

    /**
     * FilePicker JS template for displaying link/loading progress for fetching of the next page
     *
     * This text is added to .fp-content AFTER .fp-iconview/.fp-treeview/.fp-tableview
     *
     * Must have one parent element with class 'fp-nextpage'. It will be assigned additional
     * class 'loading' during loading of the next page (it is recommended that in this case the link
     * becomes unavailable). Also must contain one element <a> or <button> that will hold
     * onclick event for displaying of the next page. The event will be triggered automatically
     * when user scrolls to this link.
     *
     * @return string
     */
    protected function fp_js_template_nextpage() {
        $rv = '
<div class="fp-nextpage">
    <div class="fp-nextpage-link"><a href="#">' . get_string('more') . '</a></div>
    <div class="fp-nextpage-loading">
        ' . $this->pix_icon('i/loading_small', '') . '
    </div>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for window appearing to select a file.
     *
     * All content must be enclosed in one element, CSS for this class must define width and
     * height of the window;
     *
     * Thumbnail image will be added as content to the element with class 'fp-thumbnail';
     *
     * Inside the window the elements with the following classnames must be present:
     * 'fp-saveas', 'fp-linktype-2', 'fp-linktype-1', 'fp-linktype-4', 'fp-setauthor',
     * 'fp-setlicense'. Inside each of them must have one input element (or select in case of
     * fp-setlicense). They may also have labels.
     * The elements will be assign with class 'uneditable' and input/select element will become
     * disabled if they are not applicable for the particular file;
     *
     * There may be present elements with classes 'fp-datemodified', 'fp-datecreated', 'fp-size',
     * 'fp-license', 'fp-author', 'fp-dimensions'. They will receive additional class 'fp-unknown'
     * if information is unavailable. If there is information available, the content of embedded
     * element with class 'fp-value' will be substituted with the value;
     *
     * Elements with classes 'fp-select-confirm' and 'fp-select-cancel' will hold corresponding
     * onclick events;
     *
     * When confirm button is pressed and file is being selected, the top element receives
     * additional class 'loading'. It is removed when response from server is received.
     *
     * @return string
     */
    protected function fp_js_template_selectlayout() {
        $rv = '
<div class="file-picker fp-select">
    <div class="fp-select-loading">
        ' . $this->pix_icon('i/loading_small', '') . '
    </div>
    <form class="form-horizontal">
        <div class="fp-forminset">
                <div class="fp-linktype-2 control-group control-radio clearfix">
                    <label class="control-label control-radio">' .
                     get_string('makefileinternal', 'repository') . '</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-linktype-1 control-group control-radio clearfix">
                    <label class="control-label control-radio">' .
                     get_string('makefilelink', 'repository') . '</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-linktype-4 control-group control-radio clearfix">
                    <label class="control-label control-radio">' .
                     get_string('makefilereference', 'repository') . '</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-saveas control-group clearfix">
                    <label class="control-label">' .
                     get_string('saveas', 'repository') . '</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-setauthor control-group clearfix">
                    <label class="control-label">' .
                     get_string('author', 'repository') .
                     '</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-setlicense control-group clearfix">
                    <label class="control-label">' .
                     get_string('chooselicense', 'repository') .
                     '</label>
                    <div class="controls">
                        <select></select>
                    </div>
                </div>
            </div>
            <div class="fp-select-buttons">
                <button class="fp-select-confirm btn-primary btn">' .
                     get_string('getfile', 'repository') . '</button>
                <button class="fp-select-cancel btn-cancel btn">' .
                     get_string('cancel') .
                '</button>
            </div>
        </form>
        <div class="fp-info clearfix">
            <div class="fp-hr"></div>
            <p class="fp-thumbnail"></p>
            <div class="fp-fileinfo">
                <div class="fp-datemodified">' .
                     get_string('lastmodified', 'repository') .
                     '<span class="fp-value"></span></div>
                <div class="fp-datecreated">' .
                     get_string('datecreated', 'repository') .
                     '<span class="fp-value"></span></div>
                <div class="fp-size">' .
                     get_string('size', 'repository') .
                     '<span class="fp-value"></span></div>
                <div class="fp-license">' .
                     get_string('license', 'repository') .
                     '<span class="fp-value"></span></div>
                <div class="fp-author">' .
                     get_string('author', 'repository') .
                     '<span class="fp-value"></span></div>
                <div class="fp-dimensions">' .
                     get_string('dimensions', 'repository') . '<span class="fp-value"></span></div>
        </div>
    </div>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for 'Upload file' repository
     *
     * Content to display when user chooses 'Upload file' repository (will be nested inside
     * element with class 'fp-content').
     *
     * Must contain form (enctype="multipart/form-data" method="POST")
     *
     * The elements with the following classnames must be present:
     * 'fp-file', 'fp-saveas', 'fp-setauthor', 'fp-setlicense'. Inside each of them must have
     * one input element (or select in case of fp-setlicense). They may also have labels.
     *
     * Element with class 'fp-upload-btn' will hold onclick event for uploading the file;
     *
     * Please note that some fields may be hidden using CSS if this is part of quickupload form
     *
     * @return string
     */
    protected function fp_js_template_uploadform() {
        $rv = '
<div class="fp-upload-form">
    <div class="fp-content-center">
        <form enctype="multipart/form-data" method="POST" class="form-horizontal">
            <div class="fp-formset">
                <div class="fp-file control-group clearfix">
                    <label class="control-label">' .
                     get_string('attachment', 'repository') . '</label>
                    <div class="controls">
                        <input type="file"/>
                    </div>
                </div>
                <div class="fp-saveas control-group clearfix">
                    <label class="control-label">' .
                     get_string('saveas', 'repository') . '</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-setauthor control-group clearfix">
                    <label class="control-label">' .
                     get_string('author', 'repository') .
                     '</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <div class="fp-setlicense control-group clearfix">
                    <label class="control-label">' .
                     get_string('chooselicense', 'repository') .
                     '</label>
                    <div class="controls">
                        <select ></select>
                    </div>
                </div>
            </div>
        </form>
        <div class="mdl-align">
            <button class="fp-upload-btn btn-primary btn">' .
                     get_string('upload', 'repository') . '</button>
        </div>
    </div>
</div> ';
        return $rv;
    }

    /**
     * FilePicker JS template to display during loading process (inside element with class
     * 'fp-content').
     *
     * @return string
     */
    protected function fp_js_template_loading() {
        return '
<div class="fp-content-loading">
    <div class="fp-content-center">
        ' . $this->pix_icon('i/loading_small', '') . '
    </div>
</div>';
    }

    /**
     * FilePicker JS template for error (inside element with class 'fp-content').
     *
     * must have element with class 'fp-error', its content will be replaced with error text
     * and the error code will be assigned as additional class to this element
     * used errors: invalidjson, nofilesavailable, norepositoriesavailable
     *
     * @return string
     */
    protected function fp_js_template_error() {
        $rv = '
<div class="fp-content-error" ><div class="fp-error"></div></div>';
        return $rv;
    }

    /**
     * FilePicker JS template for error/info message displayed as a separate popup window.
     *
     * Must be wrapped in one element, CSS for this element must define
     * width and height of the window. It will be assigned with an additional class 'fp-msg-error'
     * or 'fp-msg-info' depending on message type;
     *
     * content of element with class 'fp-msg-text' will be replaced with error/info text;
     *
     * element with class 'fp-msg-butok' will hold onclick event
     *
     * @return string
     */
    protected function fp_js_template_message() {
        $rv = '
<div class="file-picker fp-msg" role="alertdialog" aria-live="assertive" aria-labelledby="fp-msg-labelledby">
    <p class="fp-msg-text" id="fp-msg-labelledby"></p>
    <button class="fp-msg-butok btn-primary btn">' . get_string('ok') . '</button>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same
     * name already exists.
     *
     * Must have one top element, CSS for this element must define width and height of the window;
     *
     * content of element with class 'fp-dlg-text' will be replaced with dialog text;
     * elements with classes 'fp-dlg-butoverwrite', 'fp-dlg-butrename',
     * 'fp-dlg-butoverwriteall', 'fp-dlg-butrenameall' and 'fp-dlg-butcancel' will
     * hold onclick events;
     *
     * content of element with class 'fp-dlg-butrename' will be substituted with appropriate string
     * (Note that it may have long text)
     *
     * @return string
     */
    protected function fp_js_template_processexistingfile() {
        $rv = '
<div class="file-picker fp-dlg">
    <p class="fp-dlg-text"></p>
    <div class="fp-dlg-buttons">
        <button class="fp-dlg-butoverwrite btn mb-1">' .
                 get_string('overwrite', 'repository') . '</button>
        <button class="fp-dlg-butrename btn mb-1"></button>
        <button class="fp-dlg-butcancel btn btn-cancel mb-1">' .
                 get_string('cancel') . '</button>
    </div>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same
     * name already exists (multiple-file version).
     *
     * Must have one top element, CSS for this element must define width and height of the window;
     *
     * content of element with class 'fp-dlg-text' will be replaced with dialog text;
     * elements with classes 'fp-dlg-butoverwrite', 'fp-dlg-butrename' and 'fp-dlg-butcancel' will
     * hold onclick events;
     *
     * content of element with class 'fp-dlg-butrename' will be substituted with appropriate string
     * (Note that it may have long text)
     *
     * @return string
     */
    protected function fp_js_template_processexistingfilemultiple() {
        $rv = '
<div class="file-picker fp-dlg">
    <p class="fp-dlg-text"></p>
    <a class="fp-dlg-butoverwrite fp-panel-button" href="#">' .
                 get_string('overwrite', 'repository') . '</a>
    <a class="fp-dlg-butcancel fp-panel-button" href="#">' .
                 get_string('cancel') . '</a>
    <a class="fp-dlg-butrename fp-panel-button" href="#"></a>
    <br/>
    <a class="fp-dlg-butoverwriteall fp-panel-button" href="#">' .
                 get_string('overwriteall', 'repository') .
                 '</a>
    <a class="fp-dlg-butrenameall fp-panel-button" href="#">' .
                 get_string('renameall', 'repository') . '</a>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for repository login form including templates for each element type
     *
     * Must contain one <form> element with templates for different input types inside:
     * Elements with classes 'fp-login-popup', 'fp-login-textarea', 'fp-login-select' and
     * 'fp-login-input' are templates for displaying respective login form elements. Inside
     * there must be exactly one element with type <button>, <textarea>, <select> or <input>
     * (i.e. fp-login-popup should have <button>, fp-login-textarea should have <textarea>, etc.);
     * They may also contain the <label> element and it's content will be substituted with
     * label;
     *
     * You can also define elements with classes 'fp-login-checkbox', 'fp-login-text'
     * but if they are not found, 'fp-login-input' will be used;
     *
     * Element with class 'fp-login-radiogroup' will be used for group of radio inputs. Inside
     * it should hava a template for one radio input (with class 'fp-login-radio');
     *
     * Element with class 'fp-login-submit' will hold on click mouse event (form submission). It
     * will be removed if at least one popup element is present;
     *
     * @return string
     */
    protected function fp_js_template_loginform() {
        $rv = '
<div class="fp-login-form">
    <div class="fp-content-center">
        <form class="form-horizontal">
            <div class="fp-formset">
                <div class="fp-login-popup control-group clearfix">
                    <div class="controls fp-popup">
                        <button class="fp-login-popup-but btn-primary btn">'.get_string('login', 'repository').'</button>
                    </div>
                </div>
                <div class="fp-login-textarea control-group clearfix">
                    <div class="controls"><textarea></textarea></div>
                </div>
                <div class="fp-login-select control-group clearfix">
                    <label class="control-label"></label>

                    <div class="controls"><select></select></div>
                </div>';
        $rv .= '
                <div class="fp-login-input control-group clearfix">
                    <label class="control-label"></label>
                    <div class="controls"><input/></div>
                </div>
                <div class="fp-login-radiogroup control-group clearfix">
                    <label class="control-label"></label>
                    <div class="controls fp-login-radio"><input /> <label></label></div>
                </div>
            </div>
            <p><button class="fp-login-submit btn-primary btn">'.get_string('submit', 'repository').'</button></p>
        </form>
    </div>
</div>';
        return $rv;
    }

    /**
     * Returns all FilePicker JavaScript templates as an array.
     *
     * @return array
     */
    public function filepicker_js_templates() {
        $classmethods = get_class_methods($this);
        $templates = array();
        foreach ($classmethods as $methodname) {
            if (preg_match('/^fp_js_template_(.*)$/', $methodname, $matches)) {
                $templates[$matches[1]] = $this->$methodname();
            }
        }
        return $templates;
    }

    /**
     * Returns HTML for default repository searchform to be passed to Filepicker
     *
     * This will be used as contents for search form defined in generallayout template
     * (form with id {TOOLSEARCHID}).
     * Default contents is one text input field with name="s"
     */
    public function repository_default_searchform() {
        $searchinput = html_writer::label(get_string('searchrepo', 'repository'), 'reposearch',
                false, array('class' => 'accesshide'
                ));
        $searchinput .= html_writer::empty_tag('input',
                array('type' => 'text', 'id' => 'reposearch', 'name' => 's',
                    'value' => get_string('search', 'repository')
                ));
        $str = html_writer::tag('div', $searchinput, array('class' => "fp-def-search"
        ));

        return $str;
    }
}
