<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item\MenuItem;

use Becklyn\Menu\Item\MenuItem;
use PHPUnit\Framework\TestCase;

class MenuItemsCurrentAncestorTest extends TestCase
{
    /**
     * Tests that the resolving of the ancestor works correctly
     */
    public function testCurrentAncestor ()
    {
        $root = new MenuItem();
        $child1 = $root->createChild("label1");
        $child2 = $root->createChild("label2");

        $child1_1 = $child1->createChild("label1.2");
        $child1_1->setCurrent(true);

        $root->resolveTree();

        self::assertTrue($child1_1->isCurrent());
        self::assertFalse($child1_1->isCurrentAncestor());

        self::assertFalse($child1->isCurrent());
        self::assertTrue($child1->isCurrentAncestor());

        self::assertFalse($child2->isCurrent());
        self::assertFalse($child2->isCurrentAncestor());

        self::assertFalse($root->isCurrent());
        self::assertTrue($root->isCurrentAncestor());
    }
}
