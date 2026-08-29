<?php

if ($argc !== 3) {
    fwrite(STDERR, "Usage: php tools/verify-coverage.php <clover.xml> <minimum-percent>\n");
    exit(2);
}

$report = $argv[1];
$minimum = (float) $argv[2];
if (! is_file($report)) {
    fwrite(STDERR, "Coverage report [{$report}] does not exist.\n");
    exit(2);
}

$xml = simplexml_load_file($report);
if ($xml === false || ! isset($xml->project->metrics)) {
    fwrite(STDERR, "Coverage report [{$report}] is invalid.\n");
    exit(2);
}

$metrics = $xml->project->metrics->attributes();
$statements = (int) $metrics['statements'];
$covered = (int) $metrics['coveredstatements'];
$percentage = $statements === 0 ? 100.0 : ($covered / $statements) * 100;

printf("Statement coverage: %.2f%% (minimum %.2f%%)\n", $percentage, $minimum);
exit($percentage + 0.00001 >= $minimum ? 0 : 1);
