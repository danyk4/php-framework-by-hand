<?php

namespace danyk\Framework\Console;

interface CommandInterface
{
    public function execute(array $parameters = []): int;
}
