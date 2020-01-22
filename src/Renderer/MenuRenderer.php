<?php declare(strict_types=1);

namespace Becklyn\Menu\Renderer;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Visitor\ItemVisitor;
use Twig\Environment;

class MenuRenderer
{
    /**
     * @var Environment
     */
    private $twig;


    /**
     * @var ItemVisitor[]|iterable
     */
    private $visitors;


    /**
     * @param ItemVisitor[] $visitors
     */
    public function __construct (Environment $twig, iterable $visitors)
    {
        $this->twig = $twig;
        $this->visitors = $visitors;
    }


    /**
     * Returns the resolved tree item for the given root
     */
    public function getResolvedItem (?MenuItem $root, array $options = []) : ?MenuItem
    {
        return null !== $root
            ? $this->resolveItem($root, $this->resolveOptions($options))
            : null;
    }


    /**
     * Renders the tree from the given root
     */
    public function render (?MenuItem $root, array $options = []) : string
    {
        if (null === $root)
        {
            return "";
        }

        // resolve template
        $template = $options["template"] ?? "@BecklynMenu/menu.html.twig";

        // resolve options
        $resolvedOptions = $this->resolveOptions($options);

        return $this->twig->render($template, [
            "options" => $resolvedOptions,
            "root" => $this->resolveItem($root, $resolvedOptions),
        ]);
    }


    /**
     * Resolves the options
     */
    private function resolveOptions (array $options) : array
    {
        $options = \array_replace([
            "translationDomain" => null,
            "currentClass" => "is-current",
            "ancestorClass" => "is-current-ancestor",
            "depth" => null,
            "key" => null,
            "rootClass" => null,
        ], $options);

        unset($options["template"]);
        return $options;
    }


    /**
     * Resolves the item
     */
    private function resolveItem (MenuItem $root, array $resolvedOptions) : MenuItem
    {
        // don't modify the original
        $root = clone $root;

        // set root class
        if (null !== $resolvedOptions["rootClass"])
        {
            $root->addChildListClass($resolvedOptions["rootClass"]);
        }

        // apply external visitors
        // must be applied before voters, as they can generate new nodes
        $visitors = $this->getSupportedVoters($resolvedOptions);

        if (!empty($visitors))
        {
            $this->applyVisitors($visitors, $root, $resolvedOptions);
        }

        // resolve the ancestors
        $root->resolveTree($resolvedOptions["currentClass"], $resolvedOptions["ancestorClass"]);

        return $root;
    }


    /**
     *
     */
    private function getSupportedVoters (array $options) : array
    {
        $result = [];

        foreach ($this->visitors as $visitor)
        {
            if ($visitor->supports($options))
            {
                $result[] = $visitor;
            }
        }

        return $result;
    }


    /**
     * Applies the visitors to the item and all children.
     *
     * @param ItemVisitor[] $visitors
     */
    private function applyVisitors (array $visitors, MenuItem $item, array $options) : void
    {
        foreach ($visitors as $visitor)
        {
            $visitor->visit($item, $options);
        }

        foreach ($item->getChildren() as $child)
        {
            $this->applyVisitors($visitors, $child, $options);
        }
    }
}
