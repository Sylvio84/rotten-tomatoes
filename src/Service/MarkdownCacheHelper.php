<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use cebe\markdown\Markdown;

class MarkdownCacheHelper
{

    protected $cache = null;
    protected $markdown = null;

    public function __construct(AdapterInterface $cache, Markdown $markdown)
    {
        $this->cache = $cache;
        $this->markdown = $markdown;
    }

    public function parse(string $content): string
    {

        $item = $this->cache->getItem("markdown" . md5($content));
        if (!$item->isHit()) {
            $item->set($this->markdown->parse($content));
            $this->cache->save($item);
        }
        return $item->get();
    }
}
