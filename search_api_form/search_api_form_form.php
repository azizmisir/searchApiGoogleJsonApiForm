<?php
//moodleform is defined in formslib.php
require_once("{$CFG->libdir}/formslib.php");

class search_api_form_form extends moodleform {
    //Add elements to form
    public function definition() {
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'search', get_string('search', 'block_search_api_form')); // Add elements to your form.
        $mform->setType('search', PARAM_RAW);
        $mform->setDefault('search', 'Moodle Blocks');        // Default value.
        $this->add_action_buttons(false, 'Suchen');
        $this->_form->disable_form_change_checker();
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
