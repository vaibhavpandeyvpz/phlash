<?php

/*
 * This file is part of vaibhavpandeyvpz/phlash package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace Phlash;

/**
 * Interface FlashInterface
 * @package Phlash
 */
interface FlashInterface
{
    /**
     * @param string $key
     * @param mixed $message
     */
    public function flashLater($key, $message);

    /**
     * @param string $key
     * @param mixed $message
     */
    public function flashNow($key, $message);

    /**
     * @param string|null $key
     *
     * @return array|mixed|null
     */
    public function get($key = null);
}
