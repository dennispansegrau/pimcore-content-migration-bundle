<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Generator;

use Twig\Environment;

readonly class CodeGenerator
{
    public function __construct(
        private Environment $twig,
        private array $templates,
    ) {
    }

    public function generate(string $templateKey, array $context = []): string
    {
        if (!isset($this->templates[$templateKey])) {
            throw new \InvalidArgumentException(sprintf('Unknown template key "%s".', $templateKey));
        }

        return $this->twig->render($this->templates[$templateKey], $context);
    }
}
