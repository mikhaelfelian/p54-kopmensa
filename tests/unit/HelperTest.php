<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class HelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Load the general helper
        helper('general');
    }

    public function testGenerateUsername()
    {
        // Test with normal input (first name only)
        $result = generateUsername('John Doe');
        $this->assertEquals(6, strlen($result));
        $this->assertMatchesRegularExpression('/^[a-z]{1,6}[0-9]*$/', $result);
        $this->assertStringStartsWith('john', $result);
        
        // Test with short input (less than 6 chars, padded with numbers)
        $result = generateUsername('Hi');
        $this->assertEquals(6, strlen($result));
        $this->assertMatchesRegularExpression('/^hi[0-9]{4}$/', $result);
        
        // Test with numbers and special characters (first name only)
        $result = generateUsername('Hello123!@#');
        $this->assertEquals(6, strlen($result));
        $this->assertMatchesRegularExpression('/^hello[0-9]$/', $result);
        
        // Test with empty string (should generate 6 random numbers)
        $result = generateUsername('');
        $this->assertEquals(6, strlen($result));
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $result);
        
        // Test with very long input (first name only, max 6 chars)
        $result = generateUsername('This is a very long name with many characters');
        $this->assertEquals(6, strlen($result));
        $this->assertMatchesRegularExpression('/^this[0-9]{2}$/', $result);
        
        // Test single word name
        $result = generateUsername('Michael');
        $this->assertEquals(6, strlen($result));
        $this->assertMatchesRegularExpression('/^michae$/', $result);
    }
}
