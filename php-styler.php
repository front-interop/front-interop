<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Styler;

return new Config(
    files: new Files(__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/impl'),
    styler: new Styler(),
    cache: __DIR__ . '/.php-styler.cache',
);
