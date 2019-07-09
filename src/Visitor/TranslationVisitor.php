<?php declare(strict_types=1);

namespace Becklyn\Menu\Visitor;

use Becklyn\Menu\Item\MenuItem;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationVisitor implements ItemVisitor
{
    /**
     * @var TranslatorInterface
     */
    private $translator;


    /**
     * @param TranslatorInterface $translator
     */
    public function __construct (TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    /**
     * @inheritDoc
     */
    public function visit (MenuItem $item, array $options) : void
    {
        $label = $item->getLabel();

        if (null !== $label)
        {
            $item->setLabel($this->translator->trans($label, [], $options["translationDomain"]));
        }
    }


    /**
     * @inheritDoc
     */
    public function supports (array $options) : bool
    {
        return null !== $options["translationDomain"];
    }
}
