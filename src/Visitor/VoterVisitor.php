<?php declare(strict_types=1);

namespace Becklyn\Menu\Visitor;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Voter\VoterInterface;

class VoterVisitor implements ItemVisitor
{
    /**
     * @var VoterInterface[]|iterable
     */
    private $voters;


    /**
     * @param VoterInterface[]|iterable $voters
     */
    public function __construct (iterable $voters)
    {
        $this->voters = $voters;
    }

    /**
     * @inheritDoc
     */
    public function visit (MenuItem $item) : void
    {
        // only apply voters if the item isn't yet marked as "current" from the construction
        if (!$item->isCurrent())
        {
            foreach ($this->voters as $voter)
            {
                $current = $voter->vote($item);

                // the first matching voter wins
                if (null !== $current)
                {
                    $item->setCurrent($current);
                    break;
                }
            }
        }
    }


    /**
     * @inheritDoc
     */
    public function supports (array $options) : bool
    {
        return \count($this->voters) > 0;
    }
}
