<?php declare(strict_types=1);

namespace Becklyn\Menu\Voter;

use Becklyn\Menu\Item\MenuItem;

interface VoterInterface
{
    /**
     * Checks whether an item is current.
     * If the voter is unable to decide it should abstain a vote and return `null`.
     *
     * @param MenuItem $item
     *
     * @return bool
     */
    public function vote (MenuItem $item) : ?bool;
}
