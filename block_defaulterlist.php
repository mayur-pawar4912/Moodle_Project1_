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
 * Form for editing HTML block instances.
 *
 * @package   block_defaulterlist
 */

class block_defaulterlist extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_defaulterlist');
    }

    
    function get_content() {

        //global variable to access database
        global $DB;
       
        
        if ($this->content !== NULL) {
            return $this->content;
        }
        //Creating empty string to store our result.
        $userstring='';
        //getting data of the tables from the database.
        $grades= $DB->get_records('grade_grades');
        $users= $DB->get_records('user');
        $courses=$DB->get_records('course');
        $items=$DB->get_records('grade_items');
        $courseid=0;
        $itemid=0;
        $userid=0;



        foreach($courses as $course){

            if($course->category > 0){
                
                $courseid=$course->id;

                //Adding course name in result
                $userstring.='<br><b> '. $course->fullname . '</b><br>' ;
            }
            else
            {

                continue;
            }

            foreach($items as $item){

                //checking the items which are related to our course and in course particularly about attendance
                if($item->courseid== $courseid && $item->itemname == "Attendance"){

                    // storing itemid of items which are related to course
                    //for which we are generating defaulter list
                    $itemid=$item->id;
                    break;

                }
            }
            foreach($grades as $grade){

                //finding grades less than 75 % and 
                //which are related to our course using itemid
                if($grade->itemid == $itemid && $grade->finalgrade <75 ){

                    //storing userid of the user whose
                    //attendance is less than 75%

                    $userid = $grade->userid;
                    foreach($users as $user){

                        //using userid which we have stored 
                        //saving username in the result.
                        if($user->id == $userid){

                            $userstring.='<br>' . $user->firstname . ' ' . $user->lastname ;

                        }
                    }
                }
            }
        }

       $this->content = new stdClass;
       //Printing our result in our defaulterlist block
        $this->content->text = $userstring;
        return $this->content;

        
    }
    
   
}
