<?php declare(strict_types=1);

namespace Becklyn\Menu\Item;

use Becklyn\Menu\Exception\InvalidTargetException;
use Becklyn\Menu\Target\RouteTarget;

class MenuItem
{
    //region Fields
    /**
     * The label to display.
     * Will be translated
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
    private $display = true;


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


    //region $this->listItemAttributes
    /**
     * @return array
     */
    public function getListItemAttributes () : array
    {
        return $this->listItemAttributes;
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


    //region $this->display
    /**
     * @return bool
     */
    public function isDisplay () : bool
    {
        return $this->display;
    }


    /**
     * @param bool $display
     *
     * @return MenuItem
     */
    public function setDisplay (bool $display) : self
    {
        $this->display = $display;
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
    public function addChild (string $name, array $options) : MenuItem
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
            ? $this->parent->getLevel()
            : 0;
    }
}
