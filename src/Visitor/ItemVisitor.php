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


    /**
     * Returns whether the visitor supports the menu item.
     * If it doesn't support it, it will not be applied in this walk.
     *
     * @param array $options    the render options
     *
     * @return bool
     */
    public function supports (array $options) : bool;
}
