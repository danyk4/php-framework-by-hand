<?php

namespace danyk\Framework\Container;

use danyk\Framework\Container\Exceptions\ContainerException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
  private array $services = [];

  public function get(string $id)
  {
    if ( ! $this->has($id)) {
      if ( ! class_exists($id)) {
        throw new ContainerException("Service $id could not be resolved");
      }

      $this->add($id);
    }

    return $this->resolve($this->services[$id]);
  }

  public function has(string $id): bool
  {
    //return isset($this->services[$id]);
    return array_key_exists($id, $this->services);   //the second variant
  }

  public function add(string $id, string|object $concrete = null)
  {
    if (is_null($concrete)) {
      if ( ! class_exists($id)) {
        throw new ContainerException("Service $id not found");
      }

      $concrete = $id;
    }

    $this->services[$id] = $concrete;
  }

  private function resolve($class)
  {
    // 1. Create class Reflection
    $reflectionClass = new \ReflectionClass($class);

    // 2. Use Reflection to receive class constructor
    $constructor = $reflectionClass->getConstructor();

    // 3. If constructor doesn`t exist create it
    if (is_null($constructor)) {
      return $reflectionClass->newInstance();
    }

    // 4. Receive constructor parameters
    $constructorParams = $constructor->getParameters();

    // 5. Receive dependencies
    $classDependencies = $this->resolveClassDependencies($constructorParams);

    // 6. Create class with dependencies
    $instance = $reflectionClass->newInstanceArgs($classDependencies);

    // 7. Return object
    return $instance;
  }

  private function resolveClassDependencies(array $constructorParams): array
  {
    // 1. Initialize empty dependencies list
    $classDependencies = [];

    // 2. Try to find and create class

    /* @var \ReflectionParameter $constructorParam */
    foreach ($constructorParams as $constructorParam) {
      // Get params
      $serviceType = $constructorParam->getType();

      // Try to create class
      $service = $this->get($serviceType->getName());

      // Add service to classDependencies
      $classDependencies[] = $service;
    }

    // 3. Return array classDependencies
    return $classDependencies;
  }

}
