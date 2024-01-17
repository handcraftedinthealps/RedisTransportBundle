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
if (!\file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}
