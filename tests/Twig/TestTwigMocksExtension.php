<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TestTwigMocksExtension extends AbstractExtension
{
    /**
     * @param string $id
     *
     * @return string
     */
    public function trans (string $id, array $parameters = [], string $domain = "messages")
    {
        return "TRANS: {$id} (in {$domain})";
    }


    /**
     * @inheritDoc
     */
    public function getFilters ()
    {
        return [
            new TwigFilter("trans", [$this, "trans"]),
        ];
    }
}
