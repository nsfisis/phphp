#!/usr/bin/env php
<?php

declare(strict_types=1);

$source = file_get_contents('php://stdin');
assert($source !== false);
$tokens = PhpToken::tokenize($source, TOKEN_PARSE);
foreach ($tokens as $token) {
    echo "{$token->getTokenName()}: {$token->text}", PHP_EOL;
}
