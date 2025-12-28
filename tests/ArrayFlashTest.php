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

use PHPUnit\Framework\TestCase;

/**
 * Class ArrayFlashTest
 *
 * Comprehensive test suite for the ArrayFlash class, covering all functionality
 * including constructor scenarios, data type handling, flash behavior, edge cases,
 * and storage reference behavior.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 */
final class ArrayFlashTest extends TestCase
{
    /**
     * Flash instance used for testing.
     */
    protected FlashInterface $flash;

    /**
     * Set up test fixtures.
     *
     * Creates a flash instance with initial data:
     * - A "later" message from previous request (warning)
     * - A "now" message (danger)
     * - A "later" message for next request (success)
     */
    protected function setUp(): void
    {
        $storage = ['Phlash' => ['later' => ['warning' => 'This is a warning message.']]];
        $this->flash = new ArrayFlash($storage);
        $this->flash->flashNow('danger', 'This is a danger message.');
        $this->flash->flashLater('success', 'This is a success message.');
    }

    /**
     * Test retrieving all flashed messages.
     *
     * Verifies that:
     * - All messages are returned as an array
     * - Messages from "later" (previous request) are available
     * - Messages from "now" (current request) are available
     * - Messages from "later" (next request) are NOT available
     */
    public function test_all(): void
    {
        $data = $this->flash->get();
        $this->assertIsArray($data);
        $this->assertArrayNotHasKey('success', $data);
        $this->assertArrayHasKey('warning', $data);
        $this->assertEquals('This is a warning message.', $data['warning']);
        $this->assertArrayHasKey('danger', $data);
        $this->assertEquals('This is a danger message.', $data['danger']);
    }

    /**
     * Test retrieving messages by specific key.
     *
     * Verifies that:
     * - Non-existent keys return null
     * - Existing keys return their values
     * - Values are of the correct type
     */
    public function test_by_key(): void
    {
        $this->assertNull($this->flash->get('success'));
        $this->assertIsString($warning = $this->flash->get('warning'));
        $this->assertEquals('This is a warning message.', $warning);
        $this->assertIsString($danger = $this->flash->get('danger'));
        $this->assertEquals('This is a danger message.', $danger);
    }

    /**
     * Test constructor with null storage parameter.
     *
     * Verifies that when null is passed, the constructor defaults to using $_SESSION.
     * Also verifies that data is actually stored in $_SESSION.
     */
    public function test_constructor_with_null_storage(): void
    {
        // This test verifies that null storage defaults to $_SESSION
        // We need to test the actual null case to cover line 36
        if (! isset($_SESSION)) {
            $_SESSION = [];
        }
        $flash = new ArrayFlash;
        $this->assertIsArray($flash->get());
        $this->assertEmpty($flash->get());

        // Verify it actually uses $_SESSION
        $flash->flashNow('test', 'value');
        $this->assertArrayHasKey('Phlash', $_SESSION);
        $this->assertEquals('value', $_SESSION['Phlash']['now']['test']);

        // Clean up
        unset($_SESSION['Phlash']);
    }

    /**
     * Test constructor with empty storage array.
     *
     * Verifies that an empty storage array is handled correctly and
     * results in an empty flash storage.
     */
    public function test_constructor_with_empty_storage(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $this->assertIsArray($flash->get());
        $this->assertEmpty($flash->get());
    }

    /**
     * Test that "later" data moves to "now" on construction.
     *
     * Verifies that when constructing with storage containing "later" data,
     * that data is moved to "now" and becomes available, while "later" is cleared.
     */
    public function test_constructor_with_later_data_moves_to_now(): void
    {
        $storage = ['Phlash' => ['later' => ['message' => 'From previous request']]];
        $flash = new ArrayFlash($storage);

        // Message from "later" should now be in "now"
        $this->assertEquals('From previous request', $flash->get('message'));

        // "later" should be empty after construction
        $this->assertEmpty($storage['Phlash']['later']);
    }

    /**
     * Test constructor with storage that has no "later" data.
     *
     * Verifies that when storage exists but has no "later" bag,
     * the constructor handles it gracefully.
     */
    public function test_constructor_with_no_later_data(): void
    {
        $storage = ['Phlash' => []];
        $flash = new ArrayFlash($storage);
        $this->assertIsArray($flash->get());
        $this->assertEmpty($flash->get());
    }

    /**
     * Test flashNow with string data type.
     *
     * Verifies that string values can be flashed and retrieved correctly.
     */
    public function test_flash_now_with_string(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('message', 'Hello World');

        $this->assertEquals('Hello World', $flash->get('message'));
    }

    /**
     * Test flashNow with array data type.
     *
     * Verifies that array values can be flashed and retrieved correctly,
     * maintaining array structure and values.
     */
    public function test_flash_now_with_array(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $data = ['error' => 'Invalid input', 'code' => 400];
        $flash->flashNow('errors', $data);

        $this->assertEquals($data, $flash->get('errors'));
        $this->assertIsArray($flash->get('errors'));
    }

    /**
     * Test flashNow with integer data type.
     *
     * Verifies that integer values can be flashed and retrieved correctly,
     * maintaining the integer type.
     */
    public function test_flash_now_with_integer(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('count', 42);

        $this->assertIsInt($flash->get('count'));
        $this->assertEquals(42, $flash->get('count'));
    }

    /**
     * Test flashNow with boolean data type.
     *
     * Verifies that boolean values (both true and false) can be flashed
     * and retrieved correctly.
     */
    public function test_flash_now_with_boolean(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('isValid', true);
        $flash->flashNow('isError', false);

        $this->assertTrue($flash->get('isValid'));
        $this->assertFalse($flash->get('isError'));
    }

    /**
     * Test flashNow with null value.
     *
     * Verifies that null values can be stored and retrieved. Since the
     * get() method uses ?? operator, we need to verify the key exists
     * to distinguish between null value and missing key.
     */
    public function test_flash_now_with_null(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('value', null);

        // Note: null values are stored, but ?? operator returns null for missing keys too
        // So we need to check if the key exists
        $all = $flash->get();
        $this->assertArrayHasKey('value', $all);
        $this->assertNull($all['value']);
    }

    /**
     * Test flashNow with nested array structure.
     *
     * Verifies that complex nested array structures can be flashed and
     * retrieved correctly, maintaining all nested levels.
     */
    public function test_flash_now_with_nested_array(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $nested = [
            'user' => [
                'name' => 'John',
                'email' => 'john@example.com',
                'preferences' => ['theme' => 'dark', 'lang' => 'en'],
            ],
        ];
        $flash->flashNow('data', $nested);

        $result = $flash->get('data');
        $this->assertIsArray($result);
        $this->assertEquals('John', $result['user']['name']);
        $this->assertEquals('dark', $result['user']['preferences']['theme']);
    }

    /**
     * Test that flashLater messages are not available in current request.
     *
     * Verifies that messages flashed with flashLater() are not accessible
     * in the same request instance.
     */
    public function test_flash_later_not_available_in_current_request(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashLater('message', 'This will be available next request');

        // flashLater should NOT be available in current request
        $this->assertNull($flash->get('message'));
        $all = $flash->get();
        $this->assertArrayNotHasKey('message', $all);
    }

    /**
     * Test that flashLater messages are available in next request.
     *
     * Verifies that messages flashed with flashLater() become available
     * when a new instance is created (simulating the next request).
     */
    public function test_flash_later_available_in_next_request(): void
    {
        $storage = [];
        $flash1 = new ArrayFlash($storage);
        $flash1->flashLater('message', 'This will be available next request');

        // Simulate next request by creating a new instance
        $flash2 = new ArrayFlash($storage);

        // Now the message should be available
        $this->assertEquals('This will be available next request', $flash2->get('message'));
    }

    /**
     * Test overwriting a key with flashNow.
     *
     * Verifies that flashing to the same key multiple times with flashNow()
     * overwrites the previous value.
     */
    public function test_overwriting_key_with_flash_now(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('message', 'First message');
        $this->assertEquals('First message', $flash->get('message'));

        $flash->flashNow('message', 'Second message');
        $this->assertEquals('Second message', $flash->get('message'));
    }

    /**
     * Test overwriting a key with flashLater.
     *
     * Verifies that flashing to the same key multiple times with flashLater()
     * overwrites the previous value in the next request.
     */
    public function test_overwriting_key_with_flash_later(): void
    {
        $storage = [];
        $flash1 = new ArrayFlash($storage);
        $flash1->flashLater('message', 'First message');
        $flash1->flashLater('message', 'Second message');

        // Create new instance to simulate next request
        $flash2 = new ArrayFlash($storage);
        $this->assertEquals('Second message', $flash2->get('message'));
    }

    /**
     * Test multiple flashNow calls with different keys.
     *
     * Verifies that multiple messages can be flashed with flashNow()
     * using different keys, and all are available simultaneously.
     */
    public function test_multiple_flash_now_calls(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('error1', 'Error 1');
        $flash->flashNow('error2', 'Error 2');
        $flash->flashNow('error3', 'Error 3');

        $all = $flash->get();
        $this->assertCount(3, $all);
        $this->assertEquals('Error 1', $all['error1']);
        $this->assertEquals('Error 2', $all['error2']);
        $this->assertEquals('Error 3', $all['error3']);
    }

    /**
     * Test multiple flashLater calls with different keys.
     *
     * Verifies that multiple messages can be flashed with flashLater()
     * using different keys, and all become available in the next request.
     */
    public function test_multiple_flash_later_calls(): void
    {
        $storage = [];
        $flash1 = new ArrayFlash($storage);
        $flash1->flashLater('msg1', 'Message 1');
        $flash1->flashLater('msg2', 'Message 2');
        $flash1->flashLater('msg3', 'Message 3');

        // Not available in current request
        $this->assertEmpty($flash1->get());

        // Available in next request
        $flash2 = new ArrayFlash($storage);
        $all = $flash2->get();
        $this->assertCount(3, $all);
        $this->assertEquals('Message 1', $all['msg1']);
        $this->assertEquals('Message 2', $all['msg2']);
        $this->assertEquals('Message 3', $all['msg3']);
    }

    /**
     * Test mixed usage of flashNow and flashLater.
     *
     * Verifies that flashNow and flashLater can be used together, and that
     * only "now" messages are available in the current request, while "later"
     * messages become available in the next request.
     */
    public function test_mixed_flash_now_and_flash_later(): void
    {
        $storage = [];
        $flash1 = new ArrayFlash($storage);
        $flash1->flashNow('now1', 'Now message 1');
        $flash1->flashLater('later1', 'Later message 1');
        $flash1->flashNow('now2', 'Now message 2');
        $flash1->flashLater('later2', 'Later message 2');

        // Only "now" messages should be available
        $all = $flash1->get();
        $this->assertCount(2, $all);
        $this->assertEquals('Now message 1', $all['now1']);
        $this->assertEquals('Now message 2', $all['now2']);
        $this->assertArrayNotHasKey('later1', $all);
        $this->assertArrayNotHasKey('later2', $all);

        // In next request, "later" messages become "now"
        $flash2 = new ArrayFlash($storage);
        $all2 = $flash2->get();
        $this->assertCount(2, $all2);
        $this->assertEquals('Later message 1', $all2['later1']);
        $this->assertEquals('Later message 2', $all2['later2']);
    }

    /**
     * Test get() method with null key parameter.
     *
     * Verifies that passing null to get() returns all flashed messages
     * as an array.
     */
    public function test_get_with_null_key(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('key1', 'value1');
        $flash->flashNow('key2', 'value2');

        $result = $flash->get(null);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    /**
     * Test get() method with empty string as key.
     *
     * Verifies that empty strings can be used as keys and retrieved correctly.
     */
    public function test_get_with_empty_string_key(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('', 'empty key value');

        $this->assertEquals('empty key value', $flash->get(''));
    }

    /**
     * Test get() method with non-existent key.
     *
     * Verifies that requesting a non-existent key returns null.
     */
    public function test_get_non_existent_key(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);

        $this->assertNull($flash->get('non_existent'));
    }

    /**
     * Test that storage is modified by reference.
     *
     * Verifies that when data is flashed, the original storage array
     * is modified by reference, allowing external access to the data.
     */
    public function test_storage_reference_behavior(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('message', 'Hello');

        // Storage should be modified by reference
        $this->assertArrayHasKey('Phlash', $storage);
        $this->assertArrayHasKey('now', $storage['Phlash']);
        $this->assertEquals('Hello', $storage['Phlash']['now']['message']);
    }

    /**
     * Test multiple instances sharing the same storage.
     *
     * Verifies that multiple ArrayFlash instances can share the same storage
     * array, and that "now" messages are reset when a new instance is created
     * (simulating a new request), while "later" messages persist.
     */
    public function test_multiple_instances_share_storage(): void
    {
        $storage = [];
        $flash1 = new ArrayFlash($storage);
        $flash1->flashNow('message1', 'From instance 1');

        // flash1 should see its own message
        $this->assertEquals('From instance 1', $flash1->get('message1'));

        // Creating a new instance resets "now" (simulating new request)
        $flash2 = new ArrayFlash($storage);
        $flash2->flashNow('message2', 'From instance 2');

        // flash2 should see its own message, but not flash1's "now" message
        // (because "now" was reset when flash2 was created)
        $this->assertEquals('From instance 2', $flash2->get('message2'));
        $this->assertNull($flash2->get('message1'));

        // However, if we use flashLater, messages persist across instances
        $flash1->flashLater('persistent', 'This persists');
        $flash3 = new ArrayFlash($storage);
        $this->assertEquals('This persists', $flash3->get('persistent'));
    }

    /**
     * Test that "later" bag is cleared after construction.
     *
     * Verifies that when a new instance is created, the "later" bag
     * is cleared after its contents are moved to "now".
     */
    public function test_later_bag_cleared_after_construction(): void
    {
        $storage = ['Phlash' => ['later' => ['message' => 'Test']]];
        $flash = new ArrayFlash($storage);

        // "later" bag should be cleared
        $this->assertEmpty($storage['Phlash']['later']);
    }

    /**
     * Test flashing complex data types.
     *
     * Verifies that complex data structures containing multiple data types
     * (strings, integers, floats, booleans, null, arrays, nested arrays)
     * can be flashed and retrieved correctly, maintaining all types and structure.
     */
    public function test_complex_data_types(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);

        $complex = [
            'string' => 'text',
            'integer' => 123,
            'float' => 45.67,
            'boolean' => true,
            'null' => null,
            'array' => [1, 2, 3],
            'nested' => [
                'level1' => [
                    'level2' => 'deep value',
                ],
            ],
        ];

        $flash->flashNow('complex', $complex);
        $result = $flash->get('complex');

        $this->assertIsArray($result);
        $this->assertEquals('text', $result['string']);
        $this->assertEquals(123, $result['integer']);
        $this->assertEquals(45.67, $result['float']);
        $this->assertTrue($result['boolean']);
        $this->assertNull($result['null']);
        $this->assertIsArray($result['array']);
        $this->assertEquals('deep value', $result['nested']['level1']['level2']);
    }

    /**
     * Test flashing empty array as value.
     *
     * Verifies that empty arrays can be flashed and retrieved correctly.
     */
    public function test_empty_array_value(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('empty', []);

        $result = $flash->get('empty');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that flashNow messages are immediately available.
     *
     * Verifies that messages flashed with flashNow() can be retrieved
     * immediately in the same request.
     */
    public function test_flash_now_immediately_available(): void
    {
        $storage = [];
        $flash = new ArrayFlash($storage);

        // Flash and immediately retrieve
        $flash->flashNow('instant', 'Available now');
        $this->assertEquals('Available now', $flash->get('instant'));
    }

    /**
     * Test that previous "later" data is consumed on construction.
     *
     * Verifies that when a new instance is created, previous "later" messages
     * are moved to "now" and consumed, so they don't persist to the next request.
     */
    public function test_previous_later_data_preserved_on_construction(): void
    {
        $storage = ['Phlash' => ['later' => ['old' => 'Old message']]];
        $flash1 = new ArrayFlash($storage);

        // Old message should be in "now"
        $this->assertEquals('Old message', $flash1->get('old'));

        // Add new later message
        $flash1->flashLater('new', 'New message');

        // Create new instance
        $flash2 = new ArrayFlash($storage);

        // Only new message should be available (old was already consumed)
        $this->assertEquals('New message', $flash2->get('new'));
        $this->assertNull($flash2->get('old'));
    }

    /**
     * Test FlashBag enum usage.
     *
     * Verifies that the FlashBag enum is used correctly internally,
     * and that both NOW and LATER cases work as expected.
     */
    public function test_flash_bag_enum_usage(): void
    {
        // Test that FlashBag enum values are used correctly
        $storage = [];
        $flash = new ArrayFlash($storage);

        // FlashBag::NOW should work
        $flash->flashNow('nowMessage', 'Now value');
        $this->assertEquals('Now value', $flash->get('nowMessage'));

        // FlashBag::LATER should work
        $flash->flashLater('laterMessage', 'Later value');
        $this->assertNull($flash->get('laterMessage'));

        // Verify enum values are correct
        $this->assertEquals('now', FlashBag::NOW->value);
        $this->assertEquals('later', FlashBag::LATER->value);
    }

    /**
     * Test STORAGE_KEY constant.
     *
     * Verifies that the STORAGE_KEY constant matches the namespace
     * and is used correctly for storage keying.
     */
    public function test_storage_key_constant(): void
    {
        // Test that STORAGE_KEY constant is used correctly
        $storage = [];
        $flash = new ArrayFlash($storage);
        $flash->flashNow('test', 'value');

        // Verify the storage key matches the namespace
        $this->assertArrayHasKey('Phlash', $storage);
        $this->assertEquals('Phlash', ArrayFlash::STORAGE_KEY);
    }
}
