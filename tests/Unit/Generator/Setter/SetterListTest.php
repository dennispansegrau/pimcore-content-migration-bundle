<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Generator\Setter;

use PHPUnit\Framework\TestCase;
use PimcoreContentMigration\Generator\Setter\Setter;
use PimcoreContentMigration\Generator\Setter\SetterList;

final class SetterListTest extends TestCase
{
    public function testItStoresAndIteratesSetters(): void
    {
        $first = new Setter('first', 1);
        $second = new Setter('second', 2);

        $list = new SetterList([$first]);
        $returned = $list->add($second);

        self::assertSame($list, $returned);
        self::assertCount(2, $list);
        self::assertSame([$first, $second], iterator_to_array($list));
    }
}

