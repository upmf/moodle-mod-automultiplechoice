<?php

/*
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

namespace mod\automultiplechoice;
require_once __DIR__ . '/ScoringRule.php';

/**
 * Group of scoring rules
 *
 * @author François Gannaz <francois.gannaz@silecs.info>
 */
class ScoringGroup
{
    /** @var string */
    public $name = '';

    /** @var string */
    public $description = '';

    /**
     * @var array of ScoringRule
     */
    public $rules = array();

    /**
     * @var array
     */
    public $errors = array();

    /**
     * Parses a block of the config into a new ScoringGroup instance.
     *
     * @param string $block
     * @return ScoringGroup
     */
    public static function buildFromConfig($block) {
        $new = self;
        $lines = array_filter(explode("\n", $block));
        $new->name = array_shift($lines);
        while (!preg_match('/^\s*[SM]\s*;/i', $lines[0])) {
            $new->description .= array_shift($lines) . "\n";
        }
        foreach ($lines as $line) {
            $new->rules[] = ScoringRule::buildFromConfig($line);
        }
        return $new;
    }
}
