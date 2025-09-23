<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use MusicLocker\Security\InputValidator;

/**
 * Input Validator Test
 * 
 * Tests for security input validation
 */
class InputValidatorTest extends TestCase
{
    public function testValidEmail()
    {
        $result = InputValidator::email('test@example.com');
        $this->assertTrue($result['valid']);
        $this->assertEquals('test@example.com', $result['value']);
    }
    
    public function testInvalidEmail()
    {
        $result = InputValidator::email('invalid-email');
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('error', $result);
    }
    
    public function testEmptyEmail()
    {
        $result = InputValidator::email('');
        $this->assertFalse($result['valid']);
        $this->assertEquals('Email is required', $result['error']);
    }
    
    public function testValidPassword()
    {
        $result = InputValidator::password('StrongPass123');
        $this->assertTrue($result['valid']);
        $this->assertEquals('StrongPass123', $result['value']);
    }
    
    public function testWeakPassword()
    {
        $result = InputValidator::password('weak');
        $this->assertFalse($result['valid']);
        $this->assertStringContains('at least 8 characters', $result['error']);
    }
    
    public function testMusicTitle()
    {
        $result = InputValidator::musicTitle('  Test Song Title  ');
        $this->assertTrue($result['valid']);
        $this->assertEquals('Test Song Title', $result['value']);
    }
    
    public function testEmptyMusicTitle()
    {
        $result = InputValidator::musicTitle('');
        $this->assertFalse($result['valid']);
        $this->assertEquals('Title is required', $result['error']);
    }
    
    public function testRatingValidation()
    {
        // Valid ratings
        for ($i = 1; $i <= 5; $i++) {
            $result = InputValidator::rating($i);
            $this->assertTrue($result['valid']);
            $this->assertEquals($i, $result['value']);
        }
        
        // Invalid ratings
        $result = InputValidator::rating(0);
        $this->assertFalse($result['valid']);
        
        $result = InputValidator::rating(6);
        $this->assertFalse($result['valid']);
        
        // Empty rating (should be valid)
        $result = InputValidator::rating('');
        $this->assertTrue($result['valid']);
        $this->assertNull($result['value']);
    }
    
    public function testUrlValidation()
    {
        $validUrls = [
            'http://example.com',
            'https://example.com/path',
            'https://subdomain.example.com/path?query=value'
        ];
        
        foreach ($validUrls as $url) {
            $result = InputValidator::url($url);
            $this->assertTrue($result['valid'], "URL {$url} should be valid");
        }
        
        $invalidUrls = [
            'ftp://example.com',
            'javascript:alert(1)',
            'not-a-url'
        ];
        
        foreach ($invalidUrls as $url) {
            $result = InputValidator::url($url);
            $this->assertFalse($result['valid'], "URL {$url} should be invalid");
        }
        
        // Empty URL should be valid
        $result = InputValidator::url('');
        $this->assertTrue($result['valid']);
        $this->assertNull($result['value']);
    }
    
    public function testIntegerValidation()
    {
        $result = InputValidator::integer('123');
        $this->assertTrue($result['valid']);
        $this->assertEquals(123, $result['value']);
        
        $result = InputValidator::integer('123', 100, 200);
        $this->assertTrue($result['valid']);
        
        $result = InputValidator::integer('50', 100, 200);
        $this->assertFalse($result['valid']);
        
        $result = InputValidator::integer('not-a-number');
        $this->assertFalse($result['valid']);
    }
    
    public function testSearchQueryValidation()
    {
        $result = InputValidator::searchQuery('test search query');
        $this->assertTrue($result['valid']);
        $this->assertEquals('test search query', $result['value']);
        
        // Test XSS prevention
        $result = InputValidator::searchQuery('<script>alert("xss")</script>');
        $this->assertTrue($result['valid']);
        $this->assertStringNotContainsString('<script>', $result['value']);
    }
    
    public function testDateValidation()
    {
        $result = InputValidator::date('2025-01-01');
        $this->assertTrue($result['valid']);
        $this->assertEquals('2025-01-01', $result['value']);
        
        $result = InputValidator::date('invalid-date');
        $this->assertFalse($result['valid']);
        
        $result = InputValidator::date('1850-01-01');
        $this->assertFalse($result['valid']);
        
        // Empty date should be valid
        $result = InputValidator::date('');
        $this->assertTrue($result['valid']);
        $this->assertNull($result['value']);
    }
}