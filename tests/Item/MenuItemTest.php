<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item;

use Becklyn\Menu\Item\MenuItem;
use PHPUnit\Framework\TestCase;

class MenuItemTest extends TestCase
{
    /**
     *
     */
    public function testSetNullParent () : void
    {
        $parent = new MenuItem("parent");
        $child = $parent->addChild("child");

        self::assertCount(1, $parent->getChildren());
        self::assertSame($child, $parent->getChildren()[0]);
        self::assertSame($parent, $child->getParent());

        $child->setParent(null);

        self::assertCount(0, $parent->getChildren());
        self::assertNull($child->getParent());
    }


    /**
     * 
     */
    public function testSetOtherParent () : void
    {
        $parent1 = new MenuItem("parent 1");
        $parent2 = new MenuItem("parent 2");
        $child = $parent1->addChild("child");

        self::assertCount(1, $parent1->getChildren());
        self::assertCount(0, $parent2->getChildren());
        self::assertSame($child, $parent1->getChildren()[0]);
        self::assertSame($parent1, $child->getParent());

        $child->setParent($parent2);

        self::assertCount(0, $parent1->getChildren());
        self::assertCount(1, $parent2->getChildren());
        self::assertSame($child, $parent2->getChildren()[0]);
        self::assertSame($parent2, $child->getParent());
    }
}
