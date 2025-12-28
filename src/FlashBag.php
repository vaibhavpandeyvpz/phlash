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
 * Enum FlashBag
 *
 * Defines the two types of flash message bags:
 * - NOW: Messages available in the current request
 * - LATER: Messages available in the next request
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 */
enum FlashBag: string
{
    /**
     * Messages available in the current request.
     *
     * Messages stored in this bag are immediately available and can be
     * retrieved in the same request cycle.
     */
    case NOW = 'now';

    /**
     * Messages available in the next request.
     *
     * Messages stored in this bag are not available in the current request,
     * but will be moved to the NOW bag when a new instance is created,
     * simulating the next request.
     */
    case LATER = 'later';
}
