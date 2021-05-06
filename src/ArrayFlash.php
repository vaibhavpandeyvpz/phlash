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
    public function flash($bag, $key, $data)
    {
        $this->storage[$bag][$key] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key = null)
    {
        if ($key) {
            return array_key_exists($key, $this->storage['now']) ? $this->storage['now'][$key] : null;
        }
        return $this->storage['now'];
    }
}
