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
        $storage = array('Phlash' => array('later' => array('warning' => array('Warning #1'))));
        $this->flash = new ArrayFlash($storage);
        $this->flash->flashNow('danger', 'Error #1');
        $this->flash->flashNow('danger', 'Error #2');
        $this->flash->flashLater('success', 'Success #1');
        $this->flash->flashLater('success', 'Success #2');
    }

    public function testEverything()
    {
        $this->assertTrue(is_array($messages = $this->flash->getMessages()));
        $this->assertNotEmpty($messages);
        $this->assertArrayHasKey('warning', $messages);
        $this->assertEquals(1, count($messages['warning']));
        $this->assertEquals('Warning #1', $messages['warning'][0]);
        $this->assertArrayHasKey('danger', $messages);
        $this->assertEquals(2, count($messages['danger']));
        $this->assertArrayNotHasKey('success', $messages);
    }
}
