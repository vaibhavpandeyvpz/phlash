<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/phlash package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace Phlash;

/**
 * Abstract class FlashAbstract
 *
 * Base implementation of FlashInterface that provides common functionality
 * for flashing messages. Concrete implementations must implement the abstract
 * flash() method to define how data is actually stored.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 */
abstract class FlashAbstract implements FlashInterface
{
    /**
     * Flash data to a specific bag.
     *
     * This method must be implemented by concrete classes to define the actual
     * storage mechanism for flashed data.
     *
     * @param  FlashBag  $bag  The bag to store data in (NOW or LATER)
     * @param  string  $key  The key to store the data under
     * @param  mixed  $data  The data to store
     */
    abstract protected function flash(FlashBag $bag, string $key, mixed $data): void;

    /**
     * {@inheritdoc}
     *
     * @param  string  $key  The key to store the message under
     * @param  mixed  $message  The message data to flash
     */
    #[\Override]
    public function flashLater(string $key, mixed $message): void
    {
        $this->flash(FlashBag::LATER, $key, $message);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $key  The key to store the message under
     * @param  mixed  $message  The message data to flash
     */
    #[\Override]
    public function flashNow(string $key, mixed $message): void
    {
        $this->flash(FlashBag::NOW, $key, $message);
    }
}
