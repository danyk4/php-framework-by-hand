<?php

namespace danyk\Framework\Tests;

use danyk\Framework\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function setUp(): void
    {
        unset($_SESSION);
    }

    public function test_set_and_get_flash()
    {
        $session = new Session();
        $session->setFlash('success', 'Success');
        $session->setFlash('error', 'Tech Error');
        $this->assertTrue($session->hasFlash('success'));
        $this->assertTrue($session->hasFlash('error'));
        $this->assertEquals(['Success'], $session->getFlash('success'));
        $this->assertEquals(['Tech Error'], $session->getFlash('error'));
        $this->assertEquals([], $session->getFlash('warning'));
    }
}
