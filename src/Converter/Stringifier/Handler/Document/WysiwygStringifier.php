<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Document;

use function is_array;
use function is_int;
use function is_string;

use LogicException;
use Pimcore\Model\Document\Editable\Wysiwyg;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IndentTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function str_repeat;

class WysiwygStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;
    use IndentTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Wysiwyg;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Wysiwyg $value */
        $dependencies = $value->resolveDependencies();
        if (empty($dependencies)) {
            return '[]';
        }

        $indent = $this->getAndIncreaseIndent($parameters);
        $result = "[\n";

        foreach ($dependencies as $data) {
            if (!is_array($data) || !isset($data['type'], $data['id'])) {
                continue;
            }

            if (!is_string($data['type']) || !is_int($data['id'])) {
                throw new LogicException('Invalid data.');
            }

            $replacement = $this->idToDependencyString(
                $data['type'],
                $data['id'],
                $dependencyList
            );

            $result .= str_repeat(' ', $indent + 4)
                . "'{$data['type']}' => [\n"
                . str_repeat(' ', $indent + 8)
                . "{$data['id']} => {$replacement},\n"
                . str_repeat(' ', $indent + 4)
                . "],\n";
        }

        return $result . str_repeat(' ', $indent) . ']';
    }
}
