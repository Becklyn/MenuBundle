<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item\MenuItem;

use Becklyn\Menu\Item\MenuItem;
use PHPUnit\Framework\TestCase;

/**
 * All tests to children / parent relations of MenuItem
 */
class MenuItemAncestorsTest extends TestCase
{
    /**
     *
     */
    public function testSetNullParent () : void
    {
        $parent = new MenuItem("parent");
        $child = $parent->createChild("child");

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
        $child = $parent1->createChild("child");

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


    /**
     *
     */
    public function testAddNewChild () : void
    {
        $parent = new MenuItem("parent");
        $child = $parent->createChild("child");

        self::assertCount(1, $parent->getChildren());
        self::assertSame($child, $parent->getChildren()[0]);
        self::assertSame($parent, $child->getParent());

        $newChild = new MenuItem();
        $parent->addChild($newChild);

        self::assertCount(2, $parent->getChildren());
        self::assertContains($child, $parent->getChildren());
        self::assertContains($newChild, $parent->getChildren());
    }


    /**
     *
     */
    public function testAddExistingChild () : void
    {
        $parent1 = new MenuItem("parent 1");
        $parent2 = new MenuItem("parent 2");
        $child = $parent1->createChild("child");

        self::assertCount(1, $parent1->getChildren());
        self::assertCount(0, $parent2->getChildren());
        self::assertSame($child, $parent1->getChildren()[0]);
        self::assertSame($parent1, $child->getParent());

        $parent2->addChild($child);

        self::assertCount(0, $parent1->getChildren());
        self::assertCount(1, $parent2->getChildren());
        self::assertSame($child, $parent2->getChildren()[0]);
        self::assertSame($parent2, $child->getParent());
    }


    /**
     *
     */
    public function testHierarchy () : void
    {
        $root = new MenuItem();
        $parent = $root->createChild("parent");
        $child = $parent->createChild("child");
        $grandchild = $child->createChild("grandchild");
        $root->createChild("other_parent1");
        $root->createChild("other_parent2");

        self::assertSame([$root, $parent, $child, $grandchild], $grandchild->getHierarchy());
    }

}
