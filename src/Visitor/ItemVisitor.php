<?php declare(strict_types=1);

namespace Becklyn\Menu\Visitor;

use Becklyn\Menu\Item\MenuItem;

interface ItemVisitor
{
    /**
     * Visits the item.
     *
     * @param array $options the render options
     */
    public function visit (MenuItem $item, array $options) : void;


    /**
     * Returns whether the visitor supports the menu item.
     * If it doesn't support it, it will not be applied in this walk.
     *
     * @param array $options the render options
     */
    public function supports (array $options) : bool;
}
