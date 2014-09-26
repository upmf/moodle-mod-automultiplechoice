<?php

/**
 * @package    mod_automultiplechoice
 * @copyright  2014 Silecs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \mod\automultiplechoice as amc;

require_once(__DIR__ . '/locallib.php');
require_once __DIR__ . '/models/Grade.php';

global $DB, $OUTPUT, $PAGE;
/* @var $DB moodle_database */
/* @var $PAGE moodle_page */
/* @var $OUTPUT core_renderer */

$controller = new amc\Controller();
$quizz = $controller->getQuizz();
$cm = $controller->getCm();
$course = $controller->getCourse();
$output = $controller->getRenderer('grading');
$action = optional_param('action', '', PARAM_ALPHA);

require_capability('mod/automultiplechoice:view', $controller->getContext());

/// Print the page header

$PAGE->set_url('/mod/automultiplechoice/grading.php', array('id' => $cm->id));
$PAGE->requires->css(new moodle_url('assets/amc.css'));

// Output starts here
echo $output->header();

$process = new amc\Grade($quizz);
if (!$process->isGraded() || $action === 'grade') {
    $process->grade();
} else if ($action === 'anotate') {
    $process->anotate();
}

echo $process->getHtmlErrors();
foreach (amc\Log::build($quizz->id)->check('grading') as $warning) {
    echo $OUTPUT->box($warning, 'warningbox');
}

echo $OUTPUT->heading("Bilan des notes")
    . $process->getHtmlStats();
?>
<form action="?a=<?php echo $quizz->id; ?>" method="post">
<p>
    Si le résultat de la notation ne vous convient pas, vous pouvez modifier le barème puis relancer la correction.
    <input type="hidden" name="action" value="grade" />
    <button type="submit">Relancer la correction</button>
</p>
</form>

<?php
echo $OUTPUT->heading("Tableaux des notes")
    . "<p>" . $process->usersknown . " copies identifiées et " . $process->usersunknown . " non identifiées. </p>"
    . $process->getHtmlCsvLinks();

if ($process->hasAnotatedFiles()) {
    $url = $process->getFileUrl('cr/corrections/pdf/' . $process->normalizeFilename('corrections'));
    echo $OUTPUT->heading("Copies corrigées")
        . \html_writer::link($url, $process->normalizeFilename('corrections'), array('target' => '_blank'));
} else {
    ?>
    <form action="?a=<?php echo $quizz->id; ?>" method="post">
    <p>
        <input type="hidden" name="action" value="anotate" />
        <button type="submit">Générer les copies corrigées (annotées)</button>
    </p>
    </form>
    <?php
}

echo $output->footer();