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
 * Class ArrayFlashTest
 */
class ArrayFlashTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FlashInterface
     */
    protected $flash;

    public function setUp()
    {
        $storage = array('Phlash' => array('later' => array('warning' => 'This is a warning message.')));
        $this->flash = new ArrayFlash($storage);
        $this->flash->flashNow('danger', 'This is a danger message.');
        $this->flash->flashLater('success', 'This is a success message.');
    }

    public function testAll()
    {
        $data = $this->flash->get();
        $this->assertInternalType('array', $data);
        $this->assertArrayNotHasKey('success', $data);
        $this->assertArrayHasKey('warning', $data);
        $this->assertEquals('This is a warning message.', $data['warning']);
        $this->assertArrayHasKey('danger', $data);
        $this->assertEquals('This is a danger message.', $data['danger']);
    }

    public function testByKey()
    {
        $this->assertEmpty($success = $this->flash->get('success'));
        $this->assertInternalType('string', $warning = $this->flash->get('warning'));
        $this->assertEquals('This is a warning message.', $warning);
        $this->assertInternalType('string', $danger = $this->flash->get('danger'));
        $this->assertEquals('This is a danger message.', $danger);
    }
}
