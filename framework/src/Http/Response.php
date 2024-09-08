<?php

namespace danyk\Framework\Http;

class Response
{
    public function __construct(
      private string $content = '',
      private int $statusCode = 200,
      private array $headers = []
    ) {
        http_response_code($this->statusCode);
    }

    public function send(): void
    {
        echo $this->content;
    }

    public function setContent(mixed $content): Response
    {
        $this->content = $content;

        return $this;
    }
}
