<?php

namespace danyk\Framework\Console;

use Psr\Container\ContainerInterface;

class Application
{
    public function __construct(
      private ContainerInterface $container
    ) {
    }

    public function run(): int
    {
        // 1. Get command name

        $argv        = $_SERVER['argv'];
        $commandName = $argv[1] ?? null;

        // 2. Return exception, if no name

        if ( ! $commandName) {
            throw new ConsoleException('Invalid command name');
        }

        // 3. Use command name to get command class object

        /** @var CommandInterface $command */
        $command = $this->container->get("console:$commandName");

        // 4. Get options and args

        $args    = array_slice($argv, 2);
        $options = $this->parseOptions($args);

        // 5. Run command and return code status

        $status = $command->execute($options);


        return $status;
    }

    private function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--')) {
                $option              = explode('=', substr($arg, 2));
                $options[$option[0]] = $option[1] ?? true;
            }
        }

        return $options;
    }
}
