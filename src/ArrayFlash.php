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
 * Class ArrayFlash
 * @package Phlash
 */
class ArrayFlash extends FlashAbstract
{
    const STORAGE_KEY = __NAMESPACE__;

    /**
     * @var array
     */
    protected $storage;

    /**
     * ArrayFlash constructor.
     * @param array $storage
     */
    public function __construct(array &$storage = null)
    {
        if (is_null($storage)) {
            $storage = &$_SESSION;
        }
        if (empty($storage[self::STORAGE_KEY])) {
            $storage[self::STORAGE_KEY] = array();
        }
        $this->storage = &$storage[self::STORAGE_KEY];
        if (isset($this->storage['later'])) {
            $this->storage['now'] = $this->storage['later'];
        } else {
            $this->storage['now'] = array();
        }
        $this->storage['later'] = array();
    }

    /**
     * {@inheritdoc}
     */
    public function flash($bag, $key, $message)
    {
        if (isset($this->storage[$bag][$key])) {
            $this->storage[$bag][$key][] = $message;
        } else {
            $this->storage[$bag][$key] = array($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->storage['now'];
    }
}
