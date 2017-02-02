<?php

/*
 * This file is part of vaibhavpandeyvpz/phlash package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Phlash;

/**
 * Class FlashAbstract
 * @package Phlash
 */
abstract class FlashAbstract implements FlashInterface
{
    /**
     * @param string $bag
     * @param string $key
     * @param string $message
     */
    abstract protected function flash($bag, $key, $message);

    /**
     * {@inheritdoc}
     */
    public function flashLater($key, $message)
    {
        $this->flash('later', $key, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function flashNow($key, $message)
    {
        $this->flash('now', $key, $message);
    }
}
