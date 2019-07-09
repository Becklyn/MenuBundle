<?php declare(strict_types=1);

namespace Becklyn\Menu\Sorter;

use Becklyn\Menu\Exception\InvalidSortMethodException;
use Becklyn\Menu\Item\MenuItem;

class MenuItemSorter
{
    public const SORT_NONE = "none";
    public const SORT_PRIORITY = "priority";
    public const SORT_ALPHA = "alpha";


    /**
     * @param string $method
     * @param array  $items
     *
     * @return array
     */
    public static function sort (string $method, array $items) : array
    {
        if (self::SORT_NONE === $method)
        {
            return $items;
        }

        if (self::SORT_PRIORITY === $method)
        {
            return self::stableSortItems(
                $items,
                function (MenuItem $item) { return $item->getPriority(); },
                function (int $left, int $right) { return $right - $left; }
            );
        }

        if (self::SORT_ALPHA === $method)
        {
            return self::stableSortItems(
                $items,
                function (MenuItem $item) { return $item->getLabel() ?? ""; },
                "strnatcasecmp"
            );
        }


        throw new InvalidSortMethodException(\sprintf("Unsupported sort method '%s'.", $method));
    }

    /**
     * Sorts the children by desc priority (stable).
     *
     * @param MenuItem[] $items
     *
     * @return MenuItem[]
     */
    private static function stableSortItems (array $items, callable $valueFetcher, callable $compare) : array
    {
        $entries = [];

        foreach ($items as $index => $item)
        {
            $entries[] = [$index, $valueFetcher($item)];
        }

        \usort(
            $entries,
            function (array $left, array $right) use ($compare)
            {
                // sort desc by priorities. If they are falsy (= 0), then sort asc by key
                return $compare($left[1], $right[1]) ?: $left[0] - $right[0];
            }
        );

        $result = [];

        foreach ($entries as $entry)
        {
            $result[] = $items[$entry[0]];
        }

        return $result;
    }
}
