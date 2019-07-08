<?php declare(strict_types=1);

namespace Becklyn\Menu\Renderer;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Visitor\ItemVisitor;
use Becklyn\Menu\Voter\VoterInterface;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;

class MenuRenderer implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $locator;

    /**
     * @var ItemVisitor[]
     */
    private $visitors;


    /**
     * @var VoterInterface[]
     */
    private $voters;


    /**
     * @param ContainerInterface $locator
     * @param ItemVisitor[]      $visitors
     * @param VoterInterface[]   $voters
     */
    public function __construct (ContainerInterface $locator, iterable $visitors, iterable $voters)
    {
        $this->locator = $locator;
        $this->visitors = $visitors;
        $this->voters = $voters;
    }


    /**
     * @param MenuItem $root
     * @param array    $options
     *
     * @return string
     */
    public function render (?MenuItem $root, array $options = []) : string
    {
        if (null === $root)
        {
            return "";
        }

        // don't modify the original
        $root = clone $root;

        // apply external visitors
        // must be applied before voters, as they can generate new nodes
        if (!empty($this->visitors))
        {
            $this->applyVisitors($root);
        }

        // run voters
        if (!empty($this->voters))
        {
            $this->applyVoters($root);
        }

        // resolve options
        $template = $options["template"] ?? "@BecklynMenu/menu.html.twig";
        unset($options["template"]);

        $options = \array_replace([
            "translationDomain" => false,
            "currentClass" => "is-current",
            "ancestorClass" => "is-current-ancestor",
            "depth" => null,
        ], $options);

        // resolve the ancestors
        $root->resolveTree($options["currentClass"], $options["ancestorClass"]);

        return $this->locator->get(Environment::class)->render($template, [
            "options" => $options,
            "root" => $root,
        ]);
    }


    /**
     * Applies the visitors to the item and all children.
     *
     * @param MenuItem $item
     */
    private function applyVisitors (MenuItem $item) : void
    {
        foreach ($this->visitors as $visitor)
        {
            $visitor->visit($item);
        }

        foreach ($item->getChildren() as $child)
        {
            $this->applyVisitors($child);
        }
    }


    /**
     * Applies the voters to the item and all children.
     *
     * @param MenuItem $item
     */
    private function applyVoters (MenuItem $item) : void
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


        foreach ($item->getChildren() as $child)
        {
            $this->applyVoters($child);
        }
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedServices ()
    {
        return [
            Environment::class,
        ];
    }
}
