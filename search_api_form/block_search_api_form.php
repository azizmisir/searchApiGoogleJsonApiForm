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

require_once('search_api_form_form.php');

class block_search_api_form extends block_base
{

    public function init()
    {
        $this->title = get_string('pluginname', 'block_search_api_form');
    }
    
    public function get_searchApiJson($input)
    {
        $curl_session = curl_init();
        $url = "https://www.googleapis.com/customsearch/v1?key=yourAPIKEY&cx=yourCX";
        $tmp = explode(' ', $input); 
        $join = implode('+', $tmp);
        $url .= $join;
        curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_session, CURLOPT_URL, $url);
        $result = curl_exec($curl_session);
        curl_close($curl_session);
        $res = json_decode($result, true);
        $len = count($res['items']);
        $ret = '';
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str = sprintf('%s', $res['items'][$i]['title']);
            $ret .= html_writer::div($str);
            $str = sprintf('%s', $res['items'][$i]['link']);
            $ret .= html_writer::div(html_writer::tag('a', $str, array('href' => $str)));
            $str = sprintf('%s', $res['items'][$i]['snippet']);
            $ret .= html_writer::div($str);
            $ret .= html_writer::empty_tag('hr');
        }
        return $ret;
    }

    public function get_content()
    {
        global $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '&copy'. ' .... '. date('Y');
        $this->content->text = '';
        
        $mform = new search_api_form_form();

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
                    $this->content->text = 'Nothing';
			//Handle form cancel operation, if cancel button is present on form
		} else if ($fromform = $mform->get_data()) {
                         $this->content->text = $mform->render();
                         $search = $mform->get_data();
                         $this->content->text .= $this->get_searchApiJson($search->search);
			 //In this case you process validated data. $mform->get_data() returns data posted in form.
		} else {
			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
			// or on the first display of the form.

			//Set default data (if any)
			$mform->set_data($toform);

			//displays the form
			$this->content->text = $mform->render();
		}

        // Add logic here to define your template data or any other content.
        //$data = $this->get_searchApiJson();
                

        return $this->content;
    }

    /**
     * Defines in which pages this block can be added.
     *
     * @return array of the pages where the block can be added.
     */
    public function applicable_formats()
    {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => true,
            'mod' => false,
            'my' => true,
        ];
    }
}
