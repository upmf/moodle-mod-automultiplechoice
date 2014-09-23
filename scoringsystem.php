<?php

/**
 * Shows details of a particular instance of automultiplechoice
 *
 * @package    mod_automultiplechoice
 * @copyright  2013 Silecs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* @var $DB moodle_database */
/* @var $PAGE moodle_page */
/* @var $OUTPUT core_renderer */

use \mod\automultiplechoice as amc;

require_once __DIR__ . '/locallib.php';

global $OUTPUT, $PAGE, $CFG;

$controller = new amc\Controller();
$quizz = $controller->getQuizz();
$cm = $controller->getCm();
$course = $controller->getCourse();
$output = $controller->getRenderer('scoringsystem');

if (!count($quizz->questions)) {
    redirect(new moodle_url('questions.php', array('a' => $quizz->id)));
}

require_capability('mod/automultiplechoice:view', $controller->getContext());

//add_to_log($course->id, 'automultiplechoice', 'view', "scoringsystem.php?id={$cm->id}", $quizz->name, $cm->id);

// Output starts here
$PAGE->set_url('/mod/automultiplechoice/scoringsystem.php', array('id' => $cm->id));
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url('assets/scoring.js'));
$PAGE->requires->css(new moodle_url('assets/amc.css'));

echo $output->header();

if (!$quizz->validate()) {
    echo $OUTPUT->box_start('errorbox');
    echo '<p>' . get_string('someerrorswerefound') . '</p>';
    echo '<dl>';
    foreach ($quizz->errors as $field => $error) {
        $field = preg_replace('/^(.+)\[(.+)\]$/', '${1}_${2}', $field);
        echo "<dt>" . get_string($field, 'automultiplechoice') . "</dt>\n"
                . "<dd>" . get_string($error, 'automultiplechoice') . "</dd>\n";
    }
    echo "</dl>\n";
    echo $OUTPUT->box_end();
}

/**
 * @todo Filter what follows down to the footer.
 */
if ($quizz->isLocked()) {
    echo '<p class="warning">Le questionnaire est actuellement verrouillé pour éviter les modifications '
            . "entre l'impression et la correction. Vous pouvez accéder aux documents via le bouton "
            . "<em>[" . get_string('prepare', 'automultiplechoice') . "]</em>.</p>";
}

// Questions
echo $OUTPUT->box_start();
echo $OUTPUT->heading("Questions");
HtmlHelper::printFormFullQuestions($quizz);

echo $OUTPUT->box_end();

echo $output->footer();
