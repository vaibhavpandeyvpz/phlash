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
 * Class ArrayFlash
 *
 * Concrete implementation of FlashInterface that stores flash messages in an array.
 * By default, it uses the $_SESSION superglobal, but can accept a custom array
 * reference for storage.
 *
 * On construction, messages from the "later" bag (from the previous request) are
 * moved to the "now" bag, making them available in the current request. The "later"
 * bag is then cleared for new messages.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 */
final class ArrayFlash extends FlashAbstract
{
    /**
     * The storage key used to namespace flash data within the storage array.
     *
     * @var string
     */
    final public const STORAGE_KEY = __NAMESPACE__;

    /**
     * Internal storage array containing flash messages.
     *
     * Structure: ['now' => ['key' => value, ...], 'later' => ['key' => value, ...]]
     *
     * @var array{now: array<string, mixed>, later: array<string, mixed>}
     */
    protected array $storage;

    /**
     * ArrayFlash constructor.
     *
     * Initializes the flash storage. If no storage is provided, defaults to $_SESSION.
     * On construction, any messages in the "later" bag are moved to "now" (simulating
     * the transition from previous request to current request), and "later" is cleared.
     *
     * @param  array<string, mixed>|null  $storage  Optional array reference for storage.
     *                                              If null, uses $_SESSION. The array is
     *                                              modified by reference.
     */
    public function __construct(?array &$storage = null)
    {
        if ($storage === null) {
            $storage = &$_SESSION;
        }
        $storage[self::STORAGE_KEY] ??= [];
        $this->storage = &$storage[self::STORAGE_KEY];

        $this->storage['now'] = $this->storage['later'] ?? [];
        $this->storage['later'] = [];
    }

    /**
     * {@inheritdoc}
     *
     * Stores data in the specified bag (NOW or LATER) under the given key.
     *
     * @param  FlashBag  $bag  The bag to store data in (NOW or LATER)
     * @param  string  $key  The key to store the data under
     * @param  mixed  $data  The data to store
     */
    #[\Override]
    protected function flash(FlashBag $bag, string $key, mixed $data): void
    {
        $this->storage[$bag->value][$key] = $data;
    }

    /**
     * {@inheritdoc}
     *
     * Retrieves flashed messages from the "now" bag. Messages that were flashed
     * with flashLater() in the previous request are available here (they were
     * moved from "later" to "now" during construction).
     *
     * @param  string|null  $key  Optional key to retrieve a specific message.
     *                            If null, returns all messages from the "now" bag.
     * @return array<string, mixed>|mixed|null Returns:
     *                                         - array<string, mixed> when $key is null (all messages)
     *                                         - mixed when $key is provided and exists (the message value)
     *                                         - null when $key is provided but doesn't exist
     */
    #[\Override]
    public function get(?string $key = null): mixed
    {
        return match ($key) {
            null => $this->storage['now'],
            default => $this->storage['now'][$key] ?? null,
        };
    }
}
