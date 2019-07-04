<?php declare(strict_types=1);

namespace Becklyn\Menu\Item;

use Becklyn\Menu\Exception\InvalidTargetException;
use Becklyn\Menu\Target\RouteTarget;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MenuItem
{
    //region Fields
    /**
     * The label to display.
     * Will be translated using the translation domain given in the renderer.
     *
     * @var string
     */
    private $label;


    /**
     * The parent menu item.
     *
     * @var MenuItem|null
     */
    private $parent;


    /**
     * The priority of this menu item. Menu items will be ordered by descending priority.
     *
     * @var int|null
     */
    private $priority = null;


    /**
     * The attributes of the list item.
     *
     * @var array
     */
    private $listItemAttributes = [];


    /**
     * The attributes of the link / the label.
     *
     * @var array
     */
    private $linkAttributes = [];


    /**
     * The attributes of the list of children.
     *
     * @var array
     */
    private $childListAttributes = [];


    /**
     * The target of this item.
     *
     * RouteTarget  -> route
     * string       -> direct URI
     * null         -> no link
     *
     * @var RouteTarget|string|null
     */
    private $target;


    /**
     * The extra attributes on the menu item.
     *
     * @var array
     */
    private $extras = [];


    /**
     * Whether the item is displayed.
     *
     * @var bool
     */
    private $visible = true;


    /**
     * Whether the item is the currently selected menu item.
     *
     * @var bool
     */
    private $current = false;


    /**
     * The children of the menu item
     *
     * @var MenuItem[]
     */
    private $children = [];
    //endregion


    /**
     * @param string|int $key
     */
    public function __construct (string $label, array $options = [])
    {
        $this->label = $label;

        if (isset($options["priority"]))
        {
            $this->setPriority($options["priority"]);
        }

        if (isset($options["listItemAttributes"]))
        {
            $this->setListItemAttributes($options["listItemAttributes"]);
        }

        if (isset($options["linkAttributes"]))
        {
            $this->setLinkAttributes($options["linkAttributes"]);
        }

        if (isset($options["childListAttributes"]))
        {
            $this->setChildListAttributes($options["childListAttributes"]);
        }

        if (isset($options["target"]))
        {
            $this->setTarget($options["target"]);
        }
        elseif (isset($options["route"]))
        {
            $this->setTarget(new RouteTarget($options["route"], $options["routeParameters"] ?? []));
        }
        elseif (isset($options["uri"]))
        {
            $this->setTarget($options["uri"]);
        }

        if (isset($options["visible"]))
        {
            $this->setVisible($options["visible"]);
        }

        if (isset($options["current"]))
        {
            $this->setCurrent($options["current"]);
        }
    }


    //region Accessors
    /**
     * @return string
     */
    public function getLabel () : string
    {
        return $this->label;
    }


    /**
     * @return MenuItem|null
     */
    public function getParent () : ?MenuItem
    {
        return $this->parent;
    }


    //region $this->priority
    /**
     * @return int|null
     */
    public function getPriority () : ?int
    {
        return $this->priority;
    }


    /**
     * @param int|null $priority
     */
    public function setPriority (?int $priority) : void
    {
        $this->priority = $priority;
    }
    //endregion


    //region $this->listItemAttributes
    /**
     * @return array
     */
    public function getListItemAttributes () : array
    {
        return $this->listItemAttributes;
    }


    /**
     * @param array $listItemAttributes
     */
    public function setListItemAttributes (array $listItemAttributes) : self
    {
        $this->listItemAttributes = $listItemAttributes;
        return $this;
    }


    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
    public function setListItemAttribute (string $name, $value) : self
    {
        $this->listItemAttributes[$name] = $value;
        return $this;
    }
    //endregion


    //region $this->linkAttributes
    /**
     * @return array
     */
    public function getLinkAttributes () : array
    {
        return $this->linkAttributes;
    }


    /**
     * @param array $linkAttributes
     */
    public function setLinkAttributes (array $linkAttributes) : self
    {
        $this->linkAttributes = $linkAttributes;
        return $this;
    }


    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
    public function setLinkAttribute (string $name, $value) : self
    {
        $this->linkAttributes[$name] = $value;
        return $this;
    }
    //endregion


    //region $this->childListAttributes
    /**
     * @return array
     */
    public function getChildListAttributes () : array
    {
        return $this->childListAttributes;
    }


    /**
     * @param array $childListAttributes
     */
    public function setChildListAttributes (array $childListAttributes) : self
    {
        $this->childListAttributes = $childListAttributes;
        return $this;
    }


    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
    public function setChildListAttribute (string $name, $value) : self
    {
        $this->childListAttributes[$name] = $value;
        return $this;
    }
    //endregion


    //region $this->target
    /**
     * @param RouteTarget|string|null $target
     *
     * @return MenuItem
     */
    public function setTarget ($target) : self
    {
        if ($target instanceof RouteTarget || \is_string($target) || null === $target)
        {
            $this->target = $target;
            return $this;
        }

        throw new InvalidTargetException($target);
    }


    /**
     * @return RouteTarget|string|null
     */
    public function getTarget ()
    {
        return $this->target;
    }
    //endregion


    //region $this->extras
    /**
     * @return array
     */
    public function getExtras () : array
    {
        return $this->extras;
    }


    /**
     * @param string $name
     * @param        $value
     *
     * @return MenuItem
     */
    public function setExtras (string $name, $value) : self
    {
        $this->extras[$name] = $value;
        return $this;
    }


    /**
     * @param string $name
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getExtra (string $name, $defaultValue = null)
    {
        return $this->extras[$name] ?? $defaultValue;
    }
    //endregion


    //region $this->visible
    /**
     * @return bool
     */
    public function isVisible () : bool
    {
        return $this->visible;
    }


    /**
     * @param bool $visible
     *
     * @return MenuItem
     */
    public function setVisible (bool $visible) : self
    {
        $this->visible = $visible;
        return $this;
    }
    //endregion


    //region $this->current
    /**
     * @return bool
     */
    public function isCurrent () : bool
    {
        return $this->current;
    }


    /**
     * @param bool $current
     *
     * @return MenuItem
     */
    public function setCurrent (bool $current) : self
    {
        $this->current = $current;
        return $this;
    }
    //endregion


    //region $this->children
    /**
     * @param string $name
     * @param array  $options
     */
    public function addChild (string $name, array $options = []) : MenuItem
    {
        $child = new self($name, $options);
        $child->parent = $this;
        $this->children[] = $child;

        return $child;
    }


    /**
     * @return MenuItem[]
     */
    public function getChildren () : array
    {
        return $this->children;
    }
    //endregion
    //endregion


    /**
     * @return int
     */
    public function getLevel () : int
    {
        return null !== $this->parent
            ? $this->parent->getLevel() + 1
            : 0;
    }


    /**
     * Resolves the ancestor state for this item and all sub items
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param array                 $options
     *
     * @return bool
     */
    public function resolveTree (UrlGeneratorInterface $urlGenerator, array $options) : bool
    {
        $isCurrentAncestor = false;

        // resolve all children
        foreach ($this->children as $child)
        {
            $subTreeCurrent = $child->resolveTree($urlGenerator, $options);

            if ($subTreeCurrent)
            {
                $isCurrentAncestor = true;
            }
        }

        $listItemClasses = [
            $this->listItemAttributes["class"] ?? "",
            "menu-item",
        ];

        if ($this->current)
        {
            $listItemClasses[] = $options["currentClass"];
        }

        if ($isCurrentAncestor)
        {
            $listItemClasses[] = $options["ancestorClass"];
        }

        if ($this->target instanceof RouteTarget)
        {
            $this->target = $urlGenerator->generate(
                $this->target->getRoute(),
                $this->target->getParameters(),
                $this->target->getReferenceType()
            );
        }

        $this->listItemAttributes["class"] = \trim(\implode(" ", $listItemClasses));

        $childListAttributes = $this->childListAttributes["class"] ?? "";
        $this->childListAttributes["class"] = \trim("{$childListAttributes} menu-level-{$this->getLevel()}");

        return $this->current || $isCurrentAncestor;
    }


    /**
     *
     */
    public function __clone ()
    {
        $oldChildren = $this->children;
        $this->children = [];

        foreach ($oldChildren as $child)
        {
            $this->children[] = clone $child;
        }
    }


    /**
     * @return MenuItem
     */
    public function getVisibleChildren () : array
    {
        $result = [];

        foreach ($this->children as $child)
        {
            if ($child->isVisible())
            {
                $result[] = $child;
            }
        }

        return $result;
    }
}
