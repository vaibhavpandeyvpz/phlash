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
 * Interface FlashInterface
 *
 * Defines the contract for flash message storage implementations.
 * Flash messages are temporary data that can be made available either
 * immediately (in the current request) or in the next request.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 */
interface FlashInterface
{
    /**
     * Flash a message to be available in the next request.
     *
     * Messages flashed with this method will not be available in the current
     * request, but will be available when a new instance is created (simulating
     * the next request).
     *
     * @param  string  $key  The key to store the message under
     * @param  mixed  $message  The message data to flash (can be any type)
     */
    public function flashLater(string $key, mixed $message): void;

    /**
     * Flash a message to be available in the current request.
     *
     * Messages flashed with this method are immediately available and can be
     * retrieved in the same request.
     *
     * @param  string  $key  The key to store the message under
     * @param  mixed  $message  The message data to flash (can be any type)
     */
    public function flashNow(string $key, mixed $message): void;

    /**
     * Get flashed messages.
     *
     * Retrieves flashed messages from the current request. Messages that were
     * flashed with flashLater() in the previous request are available here.
     *
     * @param  string|null  $key  Optional key to retrieve a specific message.
     *                            If null, returns all flashed messages as an array.
     * @return array<string, mixed>|mixed|null Returns:
     *                                         - array<string, mixed> when $key is null (all messages)
     *                                         - mixed when $key is provided and exists (the message value)
     *                                         - null when $key is provided but doesn't exist
     */
    public function get(?string $key = null): mixed;
}
