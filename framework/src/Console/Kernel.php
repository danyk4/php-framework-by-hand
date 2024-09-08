<?php

namespace danyk\Framework\Console;

use Psr\Container\ContainerInterface;

class Kernel
{
    public function __construct(
      private ContainerInterface $container,
      private Application $application
    ) {
    }

    public function handle(): int
    {
        // 1. Commands registration with the help of container
        $this->registerCommands();

        // 2. Launch commands
        $status = $this->application->run();

        // 3. Return code

        return 0;
    }

    private function registerCommands(): void
    {
        // 1. Register system commands


        // 2. Get all files from Commands folder

        $commandFiles = new \DirectoryIterator(__DIR__.'/Commands');
        $namespace    = $this->container->get('framework-commands-namespace');

        // 3. Go through all files

        foreach ($commandFiles as $commandFile) {
            if ( ! $commandFile->isFile()) {
                continue;
            }

            // 4. Get class name

            $command = $namespace.pathinfo($commandFile, PATHINFO_FILENAME);

            // 5. If it is subclass of CommandInterface

            if (is_subclass_of($command, CommandInterface::class)) {
                // -> add to container using ID of a command name

                $name = (new \ReflectionClass($command))
                  ->getProperty('name')
                  ->getDefaultValue();

                $this->container->add("console:$name", $command);
            }
        }
        // 6. Register user commands
    }
}
