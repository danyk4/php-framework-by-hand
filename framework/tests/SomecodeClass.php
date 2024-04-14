<?php

namespace danyk\Framework\Tests;

class SomecodeClass
{
  public function __construct(
    private readonly AnotherClass $anotherClass
  ) {
  }

  public function getAnotherClass(): AnotherClass
  {
    return $this->anotherClass;
  }
}
