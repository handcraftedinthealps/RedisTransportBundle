<?php

declare(strict_types=1);

/*
 * This file is part of Handcrafted in the Alps - Redis Transport Bundle Project.
 *
 * (c) Sulu GmbH <hello@sulu.io>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$file = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}

if (!file_exists(__DIR__ . '/var/cache')) {
    // To speed up multiple tests do this only when cache folder not exists
    if (system(sprintf('php %s ongr:es:index:create --manager location --if-not-exists', __DIR__ . '/bin/console'))) {
        throw new RuntimeException('Could not find elasticsearch instance!');
    }
}
