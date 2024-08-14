<?php
declare(strict_types=1);

namespace IfCastle\Application;

use PHPUnit\Framework\TestCase;

class ApplicationAbstractTest       extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
    }
    
    public function testRun(): void
    {
        TestApplication::run(__DIR__.'/MockApplication');
    }
}
