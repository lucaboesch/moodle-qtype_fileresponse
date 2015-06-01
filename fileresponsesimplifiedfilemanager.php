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
 * Fileresponse simplified FileManager form element
 *
 * Contains HTML class for a fileresponse simplified filemanager form element
 *
 * @package    qtype
 * @subpackage fileresponse
 * @copyright  2012 Luca Bösch luca.boesch@bfh.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once($CFG->dirroot.'/lib/pear/HTML/QuickForm/element.php');
require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot.'/repository/lib.php');

/**
 * Fileresponse simplified filemanager form element
 *
 * Fileresponse simplified filemanager lets user to upload/manage multiple files
 * @package   core_form
 * @category  form
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_fileresponsesimplifiedfilemanager extends HTML_QuickForm_element {
    /** @var string html for help button, if empty then no help will icon will be displayed. */
    public $_helpbutton = '';

    /** @var array options provided to initalize fileresponsesimplifiedfilemanager */
    // PHP doesn't support 'key' => $value1 | $value2 in class definition
    // We cannot do $_options = array('return_types'=> FILE_INTERNAL | FILE_REFERENCE);
    // So I have to set null here, and do it in constructor
    protected $_options = array('mainfile' => '', 'subdirs' => 1, 'maxbytes' => -1, 'maxfiles' => -1,
            'accepted_types' => '*', 'return_types' =>  null, 'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED);

    /**
     * Constructor
     *
     * @param string $elementName (optional) name of the fileresponsesimplifiedfilemanager
     * @param string $elementLabel (optional) fileresponsesimplifiedfilemanager label
     * @param array $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     * @param array $options set of options to initalize fileresponsesimplifiedfilemanager
     */
    function MoodleQuickForm_fileresponsesimplifiedfilemanager($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG, $PAGE;

        $options = (array)$options;
        foreach ($options as $name=>$value) {
            if (array_key_exists($name, $this->_options)) {
                $this->_options[$name] = $value;
            }
        }
        if (!empty($options['maxbytes'])) {
            $this->_options['maxbytes'] = get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes, $options['maxbytes']);
        }
        if (empty($options['return_types'])) {
            $this->_options['return_types'] = (FILE_INTERNAL | FILE_REFERENCE);
        }
        $this->_type = 'fileresponsesimplifiedfilemanager';
        parent::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                $caller->setType($arg[0], PARAM_INT);
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Sets name of fileresponsesimplifiedfilemanager
     *
     * @param string $name name of the fileresponsesimplifiedfilemanager
     */
    function setName($name) {
        $this->updateAttributes(array('name'=>$name));
    }

    /**
     * Returns name of fileresponsesimplifiedfilemanager
     *
     * @return string
     */
    function getName() {
        return $this->getAttribute('name');
    }

    /**
     * Updates fileresponsesimplifiedfilemanager attribute value
     *
     * @param string $value value to set
     */
    function setValue($value) {
        $this->updateAttributes(array('value'=>$value));
    }

    /**
     * Returns fileresponsesimplifiedfilemanager attribute value
     *
     * @return string
     */
    function getValue() {
        return $this->getAttribute('value');
    }

    /**
     * Returns maximum file size which can be uploaded
     *
     * @return int
     */
    function getMaxbytes() {
        return $this->_options['maxbytes'];
    }

    /**
     * Sets maximum file size which can be uploaded
     *
     * @param int $maxbytes file size
     */
    function setMaxbytes($maxbytes) {
        global $CFG, $PAGE;
        $this->_options['maxbytes'] = get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes, $maxbytes);
    }

    /**
     * Returns the maximum size of the area.
     *
     * @return int
     */
    function getAreamaxbytes() {
        return $this->_options['areamaxbytes'];
    }

    /**
     * Sets the maximum size of the area.
     *
     * @param int $areamaxbytes size limit
     */
    function setAreamaxbytes($areamaxbytes) {
        $this->_options['areamaxbytes'] = $areamaxbytes;
    }

    /**
     * Returns true if subdirectoy can be created, else false
     *
     * @return bool
     */
    function getSubdirs() {
        return $this->_options['subdirs'];
    }

    /**
     * Set option to create sub directory, while uploading  file
     *
     * @param bool $allow true if sub directory can be created.
     */
    function setSubdirs($allow) {
        $this->_options['subdirs'] = $allow;
    }

    /**
     * Returns maximum number of files which can be uploaded
     *
     * @return int
     */
    function getMaxfiles() {
        return $this->_options['maxfiles'];
    }

    /**
     * Sets maximum number of files which can be uploaded.
     *
     * @param int $num number of files
     */
    function setMaxfiles($num) {
        $this->_options['maxfiles'] = $num;
    }

    /**
     * Returns html for help button.
     *
     * @return string html for help button
     */
    function getHelpButton() {
        return $this->_helpbutton;
    }

    /**
     * Returns type of fileresponsesimplifiedfilemanager element
     *
     * @return string
     */
    function getElementTemplateType() {
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'default';
        }
    }

    /**
     * Returns HTML for fileresponsesimplifiedfilemanager form element.
     *
     * @return string
     */
    function toHtml() {
        global $CFG, $USER, $COURSE, $PAGE, $OUTPUT;
        require_once("$CFG->dirroot/repository/lib.php");

        // security - never ever allow guest/not logged in user to upload anything or use this element!
        if (isguestuser() or !isloggedin()) {
            print_error('noguest');
        }

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $id          = $this->_attributes['id'];
        $elname      = $this->_attributes['name'];
        $subdirs     = $this->_options['subdirs'];
        $maxbytes    = $this->_options['maxbytes'];
        $draftitemid = $this->getValue();
        $accepted_types = $this->_options['accepted_types'];

        if (empty($draftitemid)) {
            // no existing area info provided - let's use fresh new draft area
            require_once("$CFG->libdir/filelib.php");
            $this->setValue(file_get_unused_draft_itemid());
            $draftitemid = $this->getValue();
        }

        $client_id = uniqid();

        // fileresponsesimplifiedfilemanager options
        $options = new stdClass();
        $options->mainfile  = $this->_options['mainfile'];
        $options->maxbytes  = $this->_options['maxbytes'];
        $options->maxfiles  = $this->getMaxfiles();
        $options->client_id = $client_id;
        $options->itemid    = $draftitemid;
        $options->subdirs   = $this->_options['subdirs'];
        $options->target    = $id;
        $options->accepted_types = $accepted_types;
        $options->return_types = $this->_options['return_types'];
        $options->context = $PAGE->context;
        $options->areamaxbytes = $this->_options['areamaxbytes'];

        $html = $this->_getTabs();
        $frsfm = new form_fileresponsesimplifiedfilemanager($options);
        $output = $PAGE->get_renderer('core', 'files');
        $html .= $output->render($frsfm);

        $html .= html_writer::empty_tag('input', array('value' => $draftitemid, 'name' => $elname, 'type' => 'hidden'));
        // label element needs 'for' attribute work
        $html .= html_writer::empty_tag('input', array('value' => '', 'id' => 'id_'.$elname, 'type' => 'hidden'));

        return $html;
    }
}

/**
 * Data structure representing a file manager.
 *
 * This class defines the data structure for file mnager
 *
 * @package   core_form
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @todo      do not use this abstraction (skodak)
 */
class form_fileresponsesimplifiedfilemanager implements renderable {
    /** @var stdClass $options options for fileresponsesimplifiedfilemanager */
    public $options;

    /**
     * Constructor
     *
     * @param stdClass $options options for fileresponsesimplifiedfilemanager
     *   default options are:
     *       maxbytes=>-1,
     *       areamaxbytes => FILE_AREA_MAX_BYTES_UNLIMITED,
     *       maxfiles=>-1,
     *       itemid=>0,
     *       subdirs=>false,
     *       client_id=>uniqid(),
     *       acepted_types=>'*',
     *       return_types=>FILE_INTERNAL,
     *       context=>$PAGE->context,
     *       author=>fullname($USER),
     *       licenses=>array build from $CFG->licenses,
     *       defaultlicense=>$CFG->sitedefaultlicense
     */
    public function __construct(stdClass $options) {
        global $CFG, $USER, $PAGE;
        $defaults = array(
            'maxbytes'=>-1,
            'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
            'maxfiles'=>-1,
            'itemid'=>0,
            'subdirs'=>0,
            'client_id'=>uniqid(),
            'accepted_types'=>'*',
            'return_types'=>FILE_INTERNAL,
            'context'=>$PAGE->context,
            'hasauthor'=>false,
            'haslicense'=>false
            );
        foreach ($defaults as $key=>$value) {
            // Using !isset() prevents us from overwriting falsey values with defaults (as empty() did).
            if (!isset($options->$key)) {
                $options->$key = $value;
            }
        }

        $fs = get_file_storage();

        // initilise options, getting files in root path
        $this->options = file_get_drafarea_files($options->itemid, '/');

        // calculate file count
        $usercontext = context_user::instance($USER->id);
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $options->itemid, 'id', false);
        $filecount = count($files);
        $this->options->filecount = $filecount;

        // copying other options
        foreach ($options as $name=>$value) {
            $this->options->$name = $value;
        }

        // calculate the maximum file size as minimum from what is specified in filepicker options,
        // course options, global configuration and php settings
        $coursebytes = $maxbytes = 0;
        list($context, $course, $cm) = get_context_info_array($this->options->context->id);
        if (is_object($course)) {
            $coursebytes = $course->maxbytes;
        }
        if (!empty($this->options->maxbytes) && $this->options->maxbytes > 0) {
            $maxbytes = $this->options->maxbytes;
        }
        $this->options->maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes, $coursebytes, $maxbytes);

        // building file picker options
        $params = new stdClass();
        $params->accepted_types = $options->accepted_types;
        $params->return_types = $options->return_types;
        $params->context = $options->context;
        $params->env = 'fileresponsesimplifiedfilemanager';
        $params->disable_types = !empty($options->disable_types)?$options->disable_types:array();
        $filepicker_options = initialise_filepicker($params);
        // If filepicker plugins aren't allowed make sure that only upload repository is available for students
        if (!$options->allowpickerplugins) {
            foreach ($filepicker_options->repositories as $repository) {
                if  ($repository->type !== 'upload') {
                    unset ($filepicker_options->repositories[$repository->id]);
                }
            }
        }
        $this->options->filepicker = $filepicker_options;
    }

    public function get_nonjsurl() {
        global $PAGE;
        return new moodle_url('/repository/draftfiles_manager.php', array(
            'env'=>'fileresponsesimplifiedfilemanager',
            'action'=>'browse',
            'itemid'=>$this->options->itemid,
            'subdirs'=>$this->options->subdirs,
            'maxbytes'=>$this->options->maxbytes,
            'areamaxbytes' => $this->options->areamaxbytes,
            'maxfiles'=>$this->options->maxfiles,
            'ctx_id'=>$PAGE->context->id, // TODO ?
            'course'=>$PAGE->course->id, // TODO ?
            'sesskey'=>sesskey(),
            ));
    }

    /**
     * Generate all options needed by filepicker
     *
     * @param array $args including following keys
     *          context
     *          accepted_types
     *          return_types
     *
     * @return array the list of repository instances, including meta infomation, containing the following keys
     *          externallink
     *          repositories
     *          accepted_types
     */
    function initialise_fileresponsesimplifiedfilepicker($args) {
        global $CFG, $USER, $PAGE, $OUTPUT;
        static $templatesinitialized = array();
        require_once($CFG->libdir . '/licenselib.php');

        $return = new stdClass();
        $licenses = array();
        if (!empty($CFG->licenses)) {
            $array = explode(',', $CFG->licenses);
            foreach ($array as $license) {
                $l = new stdClass();
                $l->shortname = $license;
                $l->fullname = get_string($license, 'license');
                $licenses[] = $l;
            }
        }
        if (!empty($CFG->sitedefaultlicense)) {
            $return->defaultlicense = $CFG->sitedefaultlicense;
        }

        $return->licenses = $licenses;

        $return->author = fullname($USER);

        if (empty($args->context)) {
            $context = $PAGE->context;
        } else {
            $context = $args->context;
        }
        $disable_types = array();
        if (!empty($args->disable_types)) {
            $disable_types = $args->disable_types;
        }

        $user_context = context_user::instance($USER->id);

        list($context, $course, $cm) = get_context_info_array($context->id);
        $contexts = array($user_context, context_system::instance());
        if (!empty($course)) {
            // adding course context
            $contexts[] = context_course::instance($course->id);
        }
        $externallink = (int) get_config(null, 'repositoryallowexternallinks');
        $repositories = repository::get_instances(array(
                    'context' => $contexts,
                    'currentcontext' => $context,
                    'accepted_types' => $args->accepted_types,
                    'return_types' => $args->return_types,
                    'disable_types' => $disable_types
                ));

        $return->repositories = array();

        if (empty($externallink)) {
            $return->externallink = false;
        } else {
            $return->externallink = true;
        }

        $return->userprefs = array();
        $return->userprefs['recentrepository'] = get_user_preferences('filepicker_recentrepository', '');
        $return->userprefs['recentlicense'] = get_user_preferences('filepicker_recentlicense', '');
        $return->userprefs['recentviewmode'] = get_user_preferences('filepicker_recentviewmode', '');

        user_preference_allow_ajax_update('filepicker_recentrepository', PARAM_INT);
        user_preference_allow_ajax_update('filepicker_recentlicense', PARAM_SAFEDIR);
        user_preference_allow_ajax_update('filepicker_recentviewmode', PARAM_INT);


        // provided by form element
        $return->accepted_types = file_get_typegroup('extension', $args->accepted_types);
        $return->return_types = $args->return_types;
        $templates = array();
        foreach ($repositories as $repository) {
            $meta = $repository->get_meta();
            // Please note that the array keys for repositories are used within
            // JavaScript a lot, the key NEEDS to be the repository id.
            $return->repositories[$repository->id] = $meta;
            // Register custom repository template if it has one
            if (method_exists($repository, 'get_upload_template') && !array_key_exists('uploadform_' . $meta->type, $templatesinitialized)) {
                $templates['uploadform_' . $meta->type] = $repository->get_upload_template();
                $templatesinitialized['uploadform_' . $meta->type] = true;
            }
        }
        if (!array_key_exists('core', $templatesinitialized)) {
            // we need to send each filepicker template to the browser just once
            $fprenderer = $PAGE->get_renderer('core', 'files');
            $templates = array_merge($templates, $fprenderer->filepicker_js_templates());
            $templatesinitialized['core'] = true;
        }
        if (sizeof($templates)) {
            $PAGE->requires->js_init_call('M.core_filepicker.set_templates', array($templates), true);
        }
        return $return;
    }
}

class qtype_fileresponse_fileresponsesimplifiedfilemanager_renderer extends plugin_renderer_base {
        
     /**
     * Prints the file manager and initializes all necessary libraries
     *
     * <pre>
     * $fm = new form_fileresponsesimplifiedfilemanager($options);
     * $output = get_renderer('core', 'files');
     * echo $output->render($fm);
     * </pre>
     *
     * @param form_fileresponsesimplifiedfilemanager $fm File manager to render
     * @return string HTML fragment
     */
    public function render_form_fileresponsesimplifiedfilemanager($fm) {
        static $filemanagertemplateloaded;
        $html = $this->fm_print_generallayout($fm);
        $module = array(
            'name'=>'form_fileresponsesimplifiedfilemanager',
            'fullpath'=>'/question/type/fileresponse/fileresponsesimplifiedfilemanager.js',
            'requires' => array('core_filepicker', 'base', 'io-base', 'node', 'json', 'core_dndupload', 'panel', 'resize-plugin', 'dd-plugin'),
            'strings' => array(
                array('error', 'moodle'), array('info', 'moodle'), array('confirmdeletefile', 'repository'),
                array('draftareanofiles', 'repository'), array('entername', 'repository'), array('enternewname', 'repository'),
                array('invalidjson', 'repository'), array('popupblockeddownload', 'repository'),
                array('unknownoriginal', 'repository'), array('confirmdeletefolder', 'repository'),
                array('confirmdeletefilewithhref', 'repository'), array('confirmrenamefolder', 'repository'),
                array('confirmrenamefile', 'repository'), array('newfolder', 'repository'), array('edit', 'moodle')
            )
        );
        if (empty($fileresponsesimplifiedfilemanagertemplateloaded)) {
            $fileresponsesimplifiedfilemanagertemplateloaded = true;
            $this->page->requires->js_init_call('M.form_fileresponsesimplifiedfilemanager.set_templates',
                    array($this->fileresponsesimplifiedfilemanager_js_templates()), true, $module);
        }
        $this->page->requires->js_init_call('M.form_fileresponsesimplifiedfilemanager.init', array($fm->options), true, $module);

        // non javascript file manager
        $html .= '<noscript>';
        $html .= "<div><object type='text/html' data='".$fm->get_nonjsurl()."' height='160' width='600' style='border:1px solid #000'></object></div>";
        $html .= '</noscript>';


        return $html;
    }
    
    /**
     * Returns html for displaying one file manager
     *
     * The main element in HTML must have id="fileresponsesimplifiedfilemanager-{$client_id}" and
     * class="filemanager fm-loading";
     * After all necessary code on the page (both html and javascript) is loaded,
     * the class fm-loading will be removed and added class fm-loaded;
     * The main element (class=filemanager) will be assigned the following classes:
     * 'fm-maxfiles' - when fileresponsesimplifiedfilemanager has maximum allowed number of files;
     * 'fm-nofiles' - when fileresponsesimplifiedfilemanager has no files at all (although there might be folders);
     * 'fm-noitems' - when current view (folder) has no items - neither files nor folders;
     * 'fm-updating' - when current view is being updated (usually means that loading icon is to be displayed);
     * 'fm-nomkdir' - when 'Make folder' action is unavailable (empty($fm->options->subdirs) == true)
     *
     * Element with class 'filemanager-container' will be holding evens for dnd upload (dragover, etc.).
     * It will have class:
     * 'dndupload-ready' - when a file is being dragged over the browser
     * 'dndupload-over' - when file is being dragged over this filepicker (additional to 'dndupload-ready')
     * 'dndupload-uploading' - during the upload process (note that after dnd upload process is
     * over, the file manager will refresh the files list and therefore will have for a while class
     * fm-updating. Both waiting processes should look similar so the images don't jump for user)
     *
     * If browser supports Drag-and-drop, the body element will have class 'dndsupported',
     * otherwise - 'dndnotsupported';
     *
     * Element with class 'fp-content' will be populated with files list;
     * Element with class 'fp-btn-add' will hold onclick event for adding a file (opening filepicker);
     * Element with class 'fp-btn-mkdir' will hold onclick event for adding new folder;
     * Element with class 'fp-btn-download' will hold onclick event for download action;
     *
     * Element with class 'fp-path-folder' is a template for one folder in path toolbar.
     * It will hold mouse click event and will be assigned classes first/last/even/odd respectfully.
     * Parent element will receive class 'empty' when there are no folders to be displayed;
     * The content of subelement with class 'fp-path-folder-name' will be substituted with folder name;
     *
     * Element with class 'fp-viewbar' will have the class 'enabled' or 'disabled' when view mode
     * can be changed or not;
     * Inside element with class 'fp-viewbar' there are expected elements with classes
     * 'fp-vb-icons', 'fp-vb-tree' and 'fp-vb-details'. They will handle onclick events to switch
     * between the view modes, the last clicked element will have the class 'checked';
     *
     * @param form_fileresponsesimplifiedfilemanager $fm
     * @return string
     */
    private function fm_print_generallayout($fm) {
        global $OUTPUT;
        $options = $fm->options;
        $client_id = $options->client_id;
        $straddfile  = get_string('addfile', 'repository');
        $strmakedir  = get_string('makeafolder', 'moodle');
        $strdownload = get_string('downloadfolder', 'repository');
        $strloading  = get_string('loading', 'repository');
        $strdroptoupload = get_string('droptoupload', 'moodle');
        $icon_progress = $OUTPUT->pix_icon('i/loading_small', $strloading).'';
        $restrictions = $this->fm_print_restrictions($fm);
        $strdndnotsupported = get_string('dndnotsupported_insentence', 'moodle').$OUTPUT->help_icon('dndnotsupported');
        $strdndenabledinbox = get_string('dndenabled_inbox', 'moodle');
        $loading = get_string('loading', 'repository');

        $html = '
<div id="fileresponsesimplifiedfilemanager-'.$client_id.'" class="filemanager fm-loading">
    <div class="fp-restrictions">
        '.$restrictions.'
        <span class="dnduploadnotsupported-message"> - '.$strdndnotsupported.' </span>
    </div>
    <div class="fp-navbar">
        <div class="filemanager-toolbar">
            <div class="fp-toolbar">
                <div class="fp-btn-add">
                    <a role="button" title="'.$straddfile.'" href="#"><img src="'.$this->pix_url('a/add_file').'" alt="" /></a>
                </div>
                <div class="fp-btn-mkdir">
                    <a role="button" title="'.$strmakedir.'" href="#"><img src="'.$this->pix_url('a/create_folder').'" alt="" /></a>
                </div>
                <!-- <div class="fp-btn-download"> -->
                    <!--<a role="button" title="'.$strdownload.'" href="#"><img src="'.$this->pix_url('a/download_all').'" alt="" /></a>-->
                    <!-- don\'t download fix -->
                <!-- </div> -->
                <img class="fp-img-downloading" src="'.$this->pix_url('i/loading_small').'" alt="" />
            </div>
            <div class="fp-viewbar">
                <a title="'. get_string('displayicons', 'repository') .'" class="fp-vb-icons" href="#">
                    <img alt="" src="'. $this->pix_url('fp/view_icon_active', 'theme') .'" />
                </a>
                <a title="'. get_string('displaydetails', 'repository') .'" class="fp-vb-details" href="#">
                    <img alt="" src="'. $this->pix_url('fp/view_list_active', 'theme') .'" />
                </a>
                <a title="'. get_string('displaytree', 'repository') .'" class="fp-vb-tree" href="#">
                    <img alt="" src="'. $this->pix_url('fp/view_tree_active', 'theme') .'" />
                </a>
            </div>
        </div>
        <div class="fp-pathbar" style="display:none;"> <!-- don\'t download fix -->
            <span class="fp-path-folder"><a class="fp-path-folder-name" href="#"></a></span>
        </div>
    </div>
    <div class="filemanager-loading mdl-align">'.$icon_progress.'</div>
    <div class="filemanager-container" >
        <div class="fm-content-wrapper">
            <div class="fp-content"></div>
            <div class="fm-empty-container">
                <div class="dndupload-message">'.$strdndenabledinbox.'<br/><div class="dndupload-arrow"></div></div>
            </div>
            <div class="dndupload-target">'.$strdroptoupload.'<br/><div class="dndupload-arrow"></div></div>
            <div class="dndupload-progressbars"></div>
            <div class="dndupload-uploadinprogress">'.$icon_progress.'</div>
        </div>
        <div class="filemanager-updating">'.$icon_progress.'</div>
    </div>
</div>';
        return $html;
    }
    
    /**
     * FileManager JS template for displaying one file in 'icon view' mode.
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
    private function fm_js_template_iconfilename() {
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
    <a class="fp-contextmenu" href="#">'.$this->pix_icon('i/menu', '▶').'</a>
</div>';
        return $rv;
    }

    /**
     * FileManager JS template for displaying file name in 'table view' and 'tree view' modes.
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
    private function fm_js_template_listfilename() {
        $rv = '
<span class="fp-filename-icon">
    <a href="#">
    <span class="fp-icon"></span>
    <span class="fp-reficons1"></span>
    <span class="fp-reficons2"></span>
    <span class="fp-filename"></span>
    </a>
    <a class="fp-contextmenu" href="#" onclick="return false;">'.$this->pix_icon('i/menu', '▶').'</a>
</span>';
        return $rv;
    }

    /**
     * FileManager JS template for displaying 'Make new folder' dialog.
     *
     * Must be wrapped in an element, CSS for this element must define width and height of the window;
     *
     * Must have one input element with type="text" (for users to enter the new folder name);
     *
     * content of element with class 'fp-dlg-curpath' will be replaced with current path where
     * new folder is about to be created;
     * elements with classes 'fp-dlg-butcreate' and 'fp-dlg-butcancel' will hold onclick events;
     *
     * @return string
     */
    private function fm_js_template_mkdir() {
        $rv = '
<div class="filemanager fp-mkdir-dlg" role="dialog" aria-live="assertive" aria-labelledby="fp-mkdir-dlg-title">
    <div class="fp-mkdir-dlg-text">
        <label id="fp-mkdir-dlg-title">' . get_string('newfoldername', 'repository') . '</label><br/>
        <input type="text" />
    </div>
    <button class="fp-dlg-butcreate btn-primary btn">'.get_string('makeafolder').'</button>
    <button class="fp-dlg-butcancel btn-cancel btn">'.get_string('cancel').'</button>
</div>';
        return $rv;
    }

    /**
     * FileManager JS template for error/info message displayed as a separate popup window.
     *
     * @see fp_js_template_message()
     * @return string
     */
    private function fm_js_template_message() {
        return $this->fp_js_template_message();
    }

    /**
     * FileManager JS template for window with file information/actions.
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
     * There may be present elements with classes 'fp-original', 'fp-datemodified', 'fp-datecreated',
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
    private function fm_js_template_fileselectlayout() {
        global $OUTPUT;
        $strloading  = get_string('loading', 'repository');
        $iconprogress = $this->pix_icon('i/loading_small', $strloading).'';
        $rv = '
<div class="filemanager fp-select">
    <div class="fp-select-loading">
        <img src="'.$this->pix_url('i/loading_small').'" />
    </div>
    <form class="form-horizontal">
        <!-- <button class="fp-file-download">'.get_string('download').'</button> -->
        <button class="fp-file-delete">'.get_string('delete').'</button>
        <button class="fp-file-setmain">'.get_string('setmainfile', 'repository').'</button>
        <span class="fp-file-setmain-help">'.$OUTPUT->help_icon('setmainfile', 'repository').'</span>
        <button class="fp-file-zip">'.get_string('zip', 'editor').'</button>
        <button class="fp-file-unzip">'.get_string('unzip').'</button>
        <div class="fp-hr"></div>

        <div class="fp-forminset">
                <div class="fp-saveas control-group clearfix">
                    <label class="control-label">'.get_string('name', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <!-- don\'t download fix: eliminate author field -->
                <!--<div class="fp-author control-group clearfix">
                    <label class="control-label">'.get_string('author', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>-->
                <!-- don\'t download fix: eliminate license field -->
                <!--<div class="fp-license control-group clearfix">
                    <label class="control-label">'.get_string('chooselicense', 'repository').'</label>
                    <div class="controls">
                        <select></select>
                    </div>
                </div>-->
                <!-- don\'t download fix: eliminate path field -->
                <!--<div class="fp-path control-group clearfix">
                    <label class="control-label">'.get_string('path', 'repository').'</label>
                    <div class="controls">
                        <select></select>
                    </div>
                </div>-->
                <div class="fp-original control-group clearfix">
                    <label class="control-label">'.get_string('original', 'repository').'</label>
                    <div class="controls">
                        <span class="fp-originloading">'.$iconprogress.' '.$strloading.'</span><span class="fp-value"></span>
                    </div>
                </div>
                <div class="fp-reflist control-group clearfix">
                    <label class="control-label">'.get_string('referenceslist', 'repository').'</label>
                    <div class="controls">
                        <p class="fp-refcount"></p>
                        <span class="fp-reflistloading">'.$iconprogress.' '.$strloading.'</span>
                        <ul class="fp-value"></ul>
                    </div>
                </div>
        </div>
        <div class="fp-select-buttons">
            <button class="fp-file-update btn-primary btn">'.get_string('update', 'moodle').'</button>
            <button class="fp-file-cancel btn-cancel btn">'.get_string('cancel').'</button>
        </div>
    </form>
    <div class="fp-info clearfix">
        <div class="fp-hr"></div>
        <p class="fp-thumbnail"></p>
        <div class="fp-fileinfo">
            <div class="fp-datemodified">'.get_string('lastmodified', 'repository').' <span class="fp-value"></span></div>
            <div class="fp-datecreated">'.get_string('datecreated', 'repository').' <span class="fp-value"></span></div>
            <div class="fp-size">'.get_string('size', 'repository').' <span class="fp-value"></span></div>
            <div class="fp-dimensions">'.get_string('dimensions', 'repository').' <span class="fp-value"></span></div>
        </div>
    </div>
</div>';
        return $rv;
    }

    /**
     * FileManager JS template for popup confirm dialogue window.
     *
     * Must have one top element, CSS for this element must define width and height of the window;
     *
     * content of element with class 'fp-dlg-text' will be replaced with dialog text;
     * elements with classes 'fp-dlg-butconfirm' and 'fp-dlg-butcancel' will
     * hold onclick events;
     *
     * @return string
     */
    private function fm_js_template_confirmdialog() {
        $rv = '
<div class="filemanager fp-dlg">
    <div class="fp-dlg-text"></div>
    <button class="fp-dlg-butconfirm btn-primary btn">'.get_string('ok').'</button>
    <button class="fp-dlg-butcancel btn-cancel btn">'.get_string('cancel').'</button>
</div>';
        return $rv;
    }

    /**
     * Returns all FileManager JavaScript templates as an array.
     *
     * @return array
     */
    public function fileresponsesimplifiedfilemanager_js_templates() {
        $class_methods = get_class_methods($this);
        $templates = array();
        foreach ($class_methods as $method_name) {
            if (preg_match('/^fm_js_template_(.*)$/', $method_name, $matches))
            $templates[$matches[1]] = $this->$method_name();
        }
        return $templates;
    }

    /**
     * Displays restrictions for the file manager
     *
     * @param form_filemanager $fm
     * @return string
     */
    private function fm_print_restrictions($fm) {
        $maxbytes = display_size($fm->options->maxbytes);
        $strparam = (object) array('size' => $maxbytes, 'attachments' => $fm->options->maxfiles,
            'areasize' => display_size($fm->options->areamaxbytes));
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
        // TODO MDL-32020 also should say about 'File types accepted'
        return '<span>'. $maxsize . '</span>';
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
     * The content of subelement with class 'fp-path-folder-name' will be substituted with folder name;
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
    private function fp_js_template_generallayout() {
        $rv = '
<div tabindex="0" class="file-picker fp-generallayout" role="dialog" aria-live="assertive">
    <div class="fp-repo-area">
        <ul class="fp-list">
            <li class="fp-repo">
                <a href="#"><img class="fp-repo-icon" alt=" " width="16" height="16" />&nbsp;<span class="fp-repo-name"></span></a>
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
                            <img alt="" src="'.$this->pix_url('a/refresh').'" />
                        </a>
                    </div>
                    <div class="fp-tb-logout">
                        <a title="'. get_string('logout', 'repository') .'" href="#">
                            <img alt="" src="'.$this->pix_url('a/logout').'" />
                        </a>
                    </div>
                    <div class="fp-tb-manage">
                        <a title="'. get_string('settings', 'repository') .'" href="#">
                            <img alt="" src="'.$this->pix_url('a/setting').'" />
                        </a>
                    </div>
                    <div class="fp-tb-help">
                        <a title="'. get_string('help', 'repository') .'" href="#">
                            <img alt="" src="'.$this->pix_url('a/help').'" />
                        </a>
                    </div>
                    <div class="fp-tb-message"></div>
                </div>
                <div class="fp-viewbar">
                    <a role="button" title="'. get_string('displayicons', 'repository') .'" class="fp-vb-icons" href="#">
                        <img alt="" src="'. $this->pix_url('fp/view_icon_active', 'theme') .'" />
                    </a>
                    <a role="button" title="'. get_string('displaydetails', 'repository') .'" class="fp-vb-details" href="#">
                        <img alt="" src="'. $this->pix_url('fp/view_list_active', 'theme') .'" />
                    </a>
                    <a role="button" title="'. get_string('displaytree', 'repository') .'" class="fp-vb-tree" href="#">
                        <img alt="" src="'. $this->pix_url('fp/view_tree_active', 'theme') .'" />
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
    private function fp_js_template_iconfilename() {
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
    private function fp_js_template_listfilename() {
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
    private function fp_js_template_nextpage() {
        $rv = '
<div class="fp-nextpage">
    <div class="fp-nextpage-link"><a href="#">'.get_string('more').'</a></div>
    <div class="fp-nextpage-loading">
        <img src="'.$this->pix_url('i/loading_small').'" />
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
    private function fp_js_template_selectlayout() {
        $rv = '
<div class="file-picker fp-select">
    <div class="fp-select-loading">
        <img src="'.$this->pix_url('i/loading_small').'" />
    </div>
    <form class="form-horizontal">
        <div class="fp-forminset">
                <div class="fp-linktype-2 control-group control-radio clearfix">
                    <label class="control-label control-radio">'.get_string('makefileinternal', 'repository').'</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-linktype-1 control-group control-radio clearfix">
                    <label class="control-label control-radio">'.get_string('makefilelink', 'repository').'</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-linktype-4 control-group control-radio clearfix">
                    <label class="control-label control-radio">'.get_string('makefilereference', 'repository').'</label>
                    <div class="controls control-radio">
                        <input type="radio"/>
                    </div>
                </div>
                <div class="fp-saveas control-group clearfix">
                    <label class="control-label">'.get_string('saveas', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <!-- don\'t download fix: eliminate author field -->
                <!--<div class="fp-setauthor control-group clearfix">
                    <label class="control-label">'.get_string('author', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>-->
                <!-- don\'t download fix: eliminate license field -->
                <!--<div class="fp-setlicense control-group clearfix">
                    <label class="control-label">'.get_string('chooselicense', 'repository').'</label>
                    <div class="controls">
                        <select></select>
                    </div>
                </div>-->
        </div>
       <div class="fp-select-buttons">
            <button class="fp-select-confirm btn-primary btn">'.get_string('getfile', 'repository').'</button>
            <button class="fp-select-cancel btn-cancel btn">'.get_string('cancel').'</button>
        </div>
    </form>
    <div class="fp-info clearfix">
        <div class="fp-hr"></div>
        <p class="fp-thumbnail"></p>
        <div class="fp-fileinfo">
            <div class="fp-datemodified">'.get_string('lastmodified', 'repository').'<span class="fp-value"></span></div>
            <div class="fp-datecreated">'.get_string('datecreated', 'repository').'<span class="fp-value"></span></div>
            <div class="fp-size">'.get_string('size', 'repository').'<span class="fp-value"></span></div>
            <!-- don\'t download fix: eliminate license field -->
            <!--<div class="fp-license">'.get_string('license', 'repository').'<span class="fp-value"></span></div>-->
            <!-- don\'t download fix: eliminate author field -->
            <!--<div class="fp-author">'.get_string('author', 'repository').'<span class="fp-value"></span></div>-->
            <div class="fp-dimensions">'.get_string('dimensions', 'repository').'<span class="fp-value"></span></div>
        </div>
    </div>
</div>';
        return preg_replace('/\{\!\}/', '', $rv);
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
    private function fp_js_template_uploadform() {
        $rv = '
<div class="fp-upload-form">
    <div class="fp-content-center">
        <form enctype="multipart/form-data" method="POST" class="form-horizontal">
            <div class="fp-formset">
                <div class="fp-file control-group clearfix">
                    <label class="control-label">'.get_string('attachment', 'repository').'</label>
                    <div class="controls">
                        <input type="file"/>
                    </div>
                </div>
                <div class="fp-saveas control-group clearfix">
                    <label class="control-label">'.get_string('saveas', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>
                <!-- don\'t download fix: eliminate author field -->
                <!--<div class="fp-setauthor control-group clearfix">
                    <label class="control-label">'.get_string('author', 'repository').'</label>
                    <div class="controls">
                        <input type="text"/>
                    </div>
                </div>-->
                <!-- don\'t download fix: eliminate license field -->
                <!--<div class="fp-setlicense control-group clearfix">
                    <label class="control-label">'.get_string('chooselicense', 'repository').'</label>
                    <div class="controls">
                        <select ></select>
                    </div>
                </div>-->
            </div>
        </form>
        <div class="mdl-align">
            <button class="fp-upload-btn btn-primary btn">'.get_string('upload', 'repository').'</button>
        </div>
    </div>
</div> ';

        return $rv;
    }

    /**
     * FilePicker JS template to display during loading process (inside element with class 'fp-content').
     *
     * @return string
     */
    private function fp_js_template_loading() {
        return '
<div class="fp-content-loading">
    <div class="fp-content-center">
        <img src="'.$this->pix_url('i/loading_small').'" />
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
    private function fp_js_template_error() {
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
    private function fp_js_template_message() {
        $rv = '
<div class="file-picker fp-msg" role="alertdialog" aria-live="assertive" aria-labelledby="fp-msg-labelledby">
    <p class="fp-msg-text" id="fp-msg-labelledby"></p>
    <button class="fp-msg-butok btn-primary btn">'.get_string('ok').'</button>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same name already exists.
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
    private function fp_js_template_processexistingfile() {
        $rv = '
<div class="file-picker fp-dlg">
    <p class="fp-dlg-text"></p>
    <div class="fp-dlg-buttons">
        <button class="fp-dlg-butoverwrite btn">'.get_string('overwrite', 'repository').'</button>
        <button class="fp-dlg-butrename btn"></button>
        <button class="fp-dlg-butcancel btn btn-cancel">'.get_string('cancel').'</button>
    </div>
</div>';
        return $rv;
    }

    /**
     * FilePicker JS template for popup dialogue window asking for action when file with the same name already exists (multiple-file version).
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
    private function fp_js_template_processexistingfilemultiple() {
        $rv = '
<div class="file-picker fp-dlg">
    <p class="fp-dlg-text"></p>
    <a class="fp-dlg-butoverwrite fp-panel-button" href="#">'.get_string('overwrite', 'repository').'</a>
    <a class="fp-dlg-butcancel fp-panel-button" href="#">'.get_string('cancel').'</a>
    <a class="fp-dlg-butrename fp-panel-button" href="#"></a>
    <br/>
    <a class="fp-dlg-butoverwriteall fp-panel-button" href="#">'.get_string('overwriteall', 'repository').'</a>
    <a class="fp-dlg-butrenameall fp-panel-button" href="#">'.get_string('renameall', 'repository').'</a>
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
    private function fp_js_template_loginform() {
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
                </div>
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
        $class_methods = get_class_methods($this);
        $templates = array();
        foreach ($class_methods as $method_name) {
            if (preg_match('/^fp_js_template_(.*)$/', $method_name, $matches))
            $templates[$matches[1]] = $this->$method_name();
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
        $searchinput = html_writer::label(get_string('searchrepo', 'repository'),
            'reposearch', false, array('class' => 'accesshide'));
        $searchinput .= html_writer::empty_tag('input', array('type' => 'text',
            'id' => 'reposearch', 'name' => 's', 'value' => get_string('search', 'repository')));
        $str = html_writer::tag('div', $searchinput, array('class' => "fp-def-search"));

        return $str;
    }
}