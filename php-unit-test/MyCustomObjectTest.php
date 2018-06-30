<?php
require_once('MyCustomObject.php');
require_once('vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class MyCustomObjectTest extends TestCase
{
    public function testNewCustomObjectGetValue()
    {
        $fixture = new MyCustomObject( 20 );
        $this->assertEquals( 20, $fixture->getValue() );
    }
    public function testNewCustomObjectIsGreaterThen()
    {
        $fixture = new MyCustomObject( 10 );
        $this->assertTrue( $fixture->gt( 5 ) );
    }
    public function testNewCustomObjectIsNotGreaterThen()
    {
        $fixture = new MyCustomObject( 10 );
        $this->assertFalse( $fixture->gt( 15 ) );
    }
}