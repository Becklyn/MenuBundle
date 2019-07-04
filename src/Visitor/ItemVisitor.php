<?php declare(strict_types=1);

namespace Becklyn\Menu\Visitor;

use Becklyn\Menu\Item\MenuItem;

interface ItemVisitor
{
    /**
     * Visits the item.
     *
     * @param MenuItem $item
     */
    public function visit (MenuItem $item) : void;
}
