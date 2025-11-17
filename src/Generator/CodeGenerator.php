<?php

namespace PimcoreContentMigration\Generator;

use InvalidArgumentException;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use function sprintf;

use Twig\Environment;

readonly class CodeGenerator
{
    /**
     * @param Environment $twig
     * @param array<string, string> $templates
     */
    public function __construct(
        private Environment $twig,
        private array $templates,
    ) {
    }

    /**
     * @param string $templateKey
     * @param array<string, mixed> $context
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate(string $templateKey, array $context = []): string
    {
        if (!isset($this->templates[$templateKey])) {
            throw new InvalidArgumentException(sprintf('Unknown template key "%s".', $templateKey));
        }

        return $this->twig->render($this->templates[$templateKey], $context);
    }
}
