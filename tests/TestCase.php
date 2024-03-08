<?php

namespace Arandu\Reducible\Tests;

use Arandu\Reducible\Tests\Samples\Subject;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    
    public function setUp(): void
    {
        parent::setUp();

        Subject::flushReducers();
    }
}
