<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item\MenuItem;

use Becklyn\Menu\Exception\InvalidSortMethodException;
use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Sorter\MenuItemSorter;
use PHPUnit\Framework\TestCase;

class MenuItemSortTest extends TestCase
{
    private function buildMenu (string $sort) : MenuItem
    {
        $root = new MenuItem(null, ["sort" => $sort]);
        $root->createChild("a", ["priority" => 10]);
        $root->createChild("z", ["priority" => 100]);
        $root->createChild("z", ["priority" => 30]);
        $root->createChild("N", ["priority" => 25]);
        $root->createChild("y", ["priority" => 100]);
        $root->createChild("m", ["priority" => 50]);
        $root->resolveTree();

        return $root;
    }

    /**
     *
     */
    public function testNoneMethod () : void
    {
        $root = $this->buildMenu(MenuItemSorter::SORT_NONE);

        foreach (["a", "z", "z", "N", "y", "m"] as $index => $label)
        {
            self::assertSame($label, $root->getChildren()[$index]->getLabel(), "Label at index {$index}");
        }
    }

    /**
     *
     */
    public function testAlphaMethod () : void
    {
        $root = $this->buildMenu(MenuItemSorter::SORT_ALPHA);

        foreach (["a", "m", "N", "y", "z", "z"] as $index => $label)
        {
            self::assertSame($label, $root->getChildren()[$index]->getLabel(), "Label at index {$index}");
        }

        // sorting is stable, so it will keep the order of the priorities
        self::assertSame(100, $root->getChildren()[4]->getPriority());
        self::assertSame(30, $root->getChildren()[5]->getPriority());
    }

    /**
     *
     */
    public function testPriorityMethod () : void
    {
        $root = $this->buildMenu(MenuItemSorter::SORT_PRIORITY);

        // testing that the order is stable is already done with z & y
        foreach (["z", "y", "m", "z", "N", "a"] as $index => $label)
        {
            self::assertSame($label, $root->getChildren()[$index]->getLabel(), "Label at index {$index}");
        }
    }

    /**
     *
     */
    public function testInvalidMethod () : void
    {
        $this->expectException(InvalidSortMethodException::class);
        $this->buildMenu("invalid");
    }


    /**
     *
     */
    public function testNullLabelWithAlphaSort ()
    {
        $root = new MenuItem(null, ["sort" => MenuItemSorter::SORT_ALPHA]);
        $child1 = $root->createChild("a");
        $child2 = new MenuItem();
        $root->addChild($child2);
        $root->resolveTree();

        self::assertSame($child2, $root->getChildren()[0]);
        self::assertSame($child1, $root->getChildren()[1]);
    }
}
