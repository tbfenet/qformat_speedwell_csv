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
 *
 *
 * @package    qformat_speedwell_csv
 * @copyright
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_speedwell_csv extends qformat_default {

    public function provide_import() {
        return true;
    }

    public function mime_type() {
        return 'text/csv';
    }



    public function readquestions($lines) {

     //   $this->error(phpversion());
        $fp = fopen("php://temp","r+");
        foreach ($lines as $line) {
            fputs($fp,$line);
        }
        rewind($fp);
        $rows = array();
        while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {

            $rows[] = $data;
   //         $this->error(count($data));
        }

        fclose($fp);
       

        

        //foreach ($lines as $line) {
        //    $this->error($line);
        //     $row = str_getcsv($line,$delimiter = ',',$enclosure = '"');
        //     $this->error(count($row)) ;
        //    $rows[] = $row;
        //}
        $questions = array();
        $question = $this->defaultquestion();

        $rightans = -1;
        $questionNumber = 0;
        foreach ($rows as $row) {
         
            if ($row[0] == "Question Text") {
                
                continue;
            }

            
            if (!empty($row[0])) {
                $questionNumber = 0;


                $question = $this->defaultquestion();
                $question->qtype = 'multichoice';
                $question->name = $this->create_default_question_name($row[0], get_string('questionname', 'question'));
                $question->questiontext = htmlspecialchars($row[0], ENT_NOQUOTES);
                $question->questiontextformat = FORMAT_PLAIN;
                $question->generalfeedback = htmlspecialchars($row[1], ENT_NOQUOTES);
                $question->generalfeedbackformat = FORMAT_PLAIN;
                $question->single = 1;
                $question->answer = array();
                $question->fraction = array();
                $question->feedback = array();
                $question->correctfeedback = $this->text_field('');
                $question->partiallycorrectfeedback = $this->text_field('');
                $question->incorrectfeedback = $this->text_field('');

                $rightans = ord(strtolower($row[2])) - ord('a');


                $questions[] = $question;

            }

            if ($questionNumber == $rightans) {
                $question->fraction[$questionNumber] = 1;
            } else {
                $question->fraction[$questionNumber] = 0;
            }

          //  $this->error($row[5])  ;

            $question->answer[$questionNumber] = $this->text_field(htmlspecialchars($row[4], ENT_NOQUOTES));
            $question->feedback[] = $this->text_field('');


            $questionNumber = $questionNumber + 1;

        }



      // $this->error(count($questions));

        return $questions;
    }

    protected function text_field($text) {
        return array(
            'text' => htmlspecialchars(trim($text), ENT_NOQUOTES),
            'format' => FORMAT_PLAIN,
            'files' => array(),
        );
    }

    public function readquestion($lines) {
        // This is no longer needed but might still be called by default.php.
        return;
    }
}


