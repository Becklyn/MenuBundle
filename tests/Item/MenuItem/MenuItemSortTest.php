<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item\MenuItem;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Sorter\MenuItemSorter;
use PHPUnit\Framework\TestCase;

class MenuItemSortTest extends TestCase
{
    /**
     * @return MenuItem[]
     */
    private function getItems () : array
    {
        return [
            new MenuItem("a", ["priority" => 10]),
            new MenuItem("z", ["priority" => 100]),
            new MenuItem("z", ["priority" => 30]),
            new MenuItem("N", ["priority" => 25]),
            new MenuItem("y", ["priority" => 100]),
            new MenuItem("m", ["priority" => 50]),
            new MenuItem("1"),
            new MenuItem("2"),
            new MenuItem("-1", ["priority" => -10]),
            new MenuItem("-2", ["priority" => -20]),
            new MenuItem(null, ["priority" => -20]),
        ];
    }

    /**
     *
     */
    public function testSort () : void
    {
        $sorted = MenuItemSorter::sort($this->getItems());
        $labels = \array_map(function (MenuItem $item) { return $item->getLabel(); }, $sorted);

        $expected = ["y", "z", "m", "z", "N", "a", "1", "2", "-1", null, "-2"];
        self::assertSame($expected, $labels);
    }
}
