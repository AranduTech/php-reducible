<?php

namespace Arandu\Reducible\Tests\Unit;

use Arandu\Reducible\Tests\Samples\MalformedSubject;
use Arandu\Reducible\Tests\Samples\Subject;
use Arandu\Reducible\Tests\TestCase;

class ExceptionTest extends TestCase
{

    public function testReducersCannotBeCalledWithoutArguments()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->expectExceptionMessage('No value provided for reducer.');

        Subject::reducerAlpha();
    }

}
