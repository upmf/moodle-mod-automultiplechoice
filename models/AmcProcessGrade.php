<?php
/**
 * @package    mod
 * @subpackage automultiplechoice
 * @copyright  2013 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod\automultiplechoice;

require_once __DIR__ . '/AmcProcess.php';

class AmcProcessGrade extends AmcProcess
{
    /**
     * Shell-executes 'amc prepare' for extracting grading scale (Bareme)
     * @return bool
     */
    public function amcPrepareBareme() {
        $pre = $this->workdir;
        $parameters = array(
            '--n-copies', (string) $this->quizz->amcparams->copies,
            '--with', 'xelatex',
            '--filter', 'plain',
            '--mode', 'b',
            '--data', $pre . '/data',
            '--filtered-source', $pre . '/prepare-source_filtered.tex',
            '--progression-id', 'bareme',
            '--progression', '1',
            $pre . '/prepare-source.txt'
            );
        $res = $this->shellExec('auto-multiple-choice prepare', $parameters);
        if ($res) {
            $this->log('prepare:bareme', 'OK.');
        }
        return $res;
    }

    /**
     * Shell-executes 'amc note'
     * @return bool
     */
    public function amcNote() {
        $pre = $this->workdir;
        $parameters = array(
            '--data', $pre . '/data',
            '--progression-id', 'notation',
            '--progression', '1',
            '--seuil', '0.5',
            '--grain', '0.5',
            '--arrondi', 'inf',
            '--notemax', '20',
            '--plafond',
            '--notemin', '',
            '--postcorrect-student', '', //FIXME inutile ?
            '--postcorrect-copy', '',    //FIXME inutile ?
            );
        $res = $this->shellExec('auto-multiple-choice note', $parameters);
        if ($res) {
            $this->log('note', 'OK.');
        }
        return $res;
    }

    /**
     * Shell-executes 'amc export' to get a csv file
     * @return bool
     */
    public function amcExport() {
        $pre = $this->workdir;
        $parameters = array(
            '--module', 'CSV',
            '--data', $pre . '/data',
            '--useall', '',
            '--sort', 'n',
            '--fich-noms', '%PROJET/',
            '--noms-encodage', 'UTF-8',
            '--csv-build-name', '(nom|surname) (prenom|name)',
            '--no-rtl',
            '--output', $pre . '/exports/scoring.csv',
            '--option-out', 'encodage=UTF-8',
            '--option-out', 'columns=student.copy,student.key,student.name',
            '--option-out', 'decimal=,',
            '--option-out', 'ticked=',
            '--option-out', 'separateur=;',
            );
        $res = $this->shellExec('auto-multiple-choice export', $parameters);
        if ($res) {
            $this->log('export', 'scoring.csv');
        }
        return $res;
    }
}