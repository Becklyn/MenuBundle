<?php declare(strict_types=1);

namespace Becklyn\Menu\Sorter;

use Becklyn\Menu\Item\MenuItem;

class MenuItemSorter
{
    /**
     * Sorts the menu items.
     *
     * @param MenuItem[] $items
     *
     * @return MenuItem[]
     */
    public static function sort (array $items) : array
    {
        \usort(
            $items,
            function (MenuItem $left, MenuItem $right)
            {
                // if the same priority -> sort alphabetically asc
                if ($left->getPriority() === $right->getPriority())
                {
                    return \strnatcasecmp((string) $left->getLabel(), (string) $right->getLabel());
                }

                // order by priority desc by default
                return $right->getPriority() - $left->getPriority();
            }
        );

        return $items;
    }
}
