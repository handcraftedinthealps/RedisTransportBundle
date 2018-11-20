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

namespace Intosite\Bundle\LocationProjectionBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExampleTest extends WebTestCase
{
    public function setUp()
    {
        self::bootKernel();
    }

    public function testExample()
    {
        self::$kernel->getContainer();

        $this->assertSame(1, 1);
    }
}
