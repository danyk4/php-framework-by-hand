<?php

namespace danyk\Framework\Tests;

use danyk\Framework\Container\Container;
use danyk\Framework\Container\Exceptions\ContainerException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
  public function test_getting_service_from_container()
  {
    $container = new Container();

    $container->add('somecode-class', SomecodeClass::class);

    $this->assertInstanceOf(SomecodeClass::class, $container->get('somecode-class'));
  }

  public function test_container_has_exception_ContainerException_if_add_wrong_service()
  {
    $container = new Container();

    $this->expectException(ContainerException::class);

    $container->add('no-class');
  }

  public function test_has_method()
  {
    $container = new Container();

    $container->add('somecode-class', SomecodeClass::class);

    $this->assertTrue($container->has('somecode-class'));
    $this->assertFalse($container->has('no-class'));
  }

  public function test_recursively_autowired()
  {
    $container = new Container();

    $container->add('somecode-class', SomecodeClass::class);

    /* @var SomecodeClass $somecode */
    $somecode = $container->get('somecode-class');

    $anotherclass = $somecode->getAnotherClass();

    $this->assertInstanceOf(AnotherClass::class, $somecode->getAnotherClass());
    $this->assertInstanceOf(Telegram::class, $anotherclass->getTelegram());
    $this->assertInstanceOf(YouTube::class, $anotherclass->getYouTube());
  }
}
