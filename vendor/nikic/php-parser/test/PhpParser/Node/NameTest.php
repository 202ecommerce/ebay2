<?php

namespace EbayVendor\PhpParser\Node;

class NameTest extends \EbayVendor\PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $name = new Name(array('foo', 'bar'));
        $this->assertSame(array('foo', 'bar'), $name->parts);
        $name = new Name('EbayVendor\\foo\\bar');
        $this->assertSame(array('foo', 'bar'), $name->parts);
        $name = new Name($name);
        $this->assertSame(array('foo', 'bar'), $name->parts);
    }
    public function testGet()
    {
        $name = new Name('foo');
        $this->assertSame('foo', $name->getFirst());
        $this->assertSame('foo', $name->getLast());
        $name = new Name('EbayVendor\\foo\\bar');
        $this->assertSame('foo', $name->getFirst());
        $this->assertSame('bar', $name->getLast());
    }
    public function testToString()
    {
        $name = new Name('EbayVendor\\foo\\bar');
        $this->assertSame('EbayVendor\\foo\\bar', (string) $name);
        $this->assertSame('EbayVendor\\foo\\bar', $name->toString());
    }
    public function testSlice()
    {
        $name = new Name('EbayVendor\\foo\\bar\\baz');
        $this->assertEquals(new Name('EbayVendor\\foo\\bar\\baz'), $name->slice(0));
        $this->assertEquals(new Name('EbayVendor\\bar\\baz'), $name->slice(1));
        $this->assertNull($name->slice(3));
        $this->assertEquals(new Name('EbayVendor\\foo\\bar\\baz'), $name->slice(-3));
        $this->assertEquals(new Name('EbayVendor\\bar\\baz'), $name->slice(-2));
        $this->assertEquals(new Name('EbayVendor\\foo\\bar'), $name->slice(0, -1));
        $this->assertNull($name->slice(0, -3));
        $this->assertEquals(new Name('bar'), $name->slice(1, -1));
        $this->assertNull($name->slice(1, -2));
        $this->assertEquals(new Name('bar'), $name->slice(-2, 1));
        $this->assertEquals(new Name('bar'), $name->slice(-2, -1));
        $this->assertNull($name->slice(-2, -2));
    }
    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Offset 4 is out of bounds
     */
    public function testSliceOffsetTooLarge()
    {
        (new Name('EbayVendor\\foo\\bar\\baz'))->slice(4);
    }
    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Offset -4 is out of bounds
     */
    public function testSliceOffsetTooSmall()
    {
        (new Name('EbayVendor\\foo\\bar\\baz'))->slice(-4);
    }
    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Length 4 is out of bounds
     */
    public function testSliceLengthTooLarge()
    {
        (new Name('EbayVendor\\foo\\bar\\baz'))->slice(0, 4);
    }
    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Length -4 is out of bounds
     */
    public function testSliceLengthTooSmall()
    {
        (new Name('EbayVendor\\foo\\bar\\baz'))->slice(0, -4);
    }
    public function testConcat()
    {
        $this->assertEquals(new Name('EbayVendor\\foo\\bar\\baz'), Name::concat('foo', 'EbayVendor\\bar\\baz'));
        $this->assertEquals(new Name\FullyQualified('EbayVendor\\foo\\bar'), Name\FullyQualified::concat(['foo'], new Name('bar')));
        $attributes = ['foo' => 'bar'];
        $this->assertEquals(new Name\Relative('EbayVendor\\foo\\bar\\baz', $attributes), Name\Relative::concat(new Name\FullyQualified('EbayVendor\\foo\\bar'), 'baz', $attributes));
        $this->assertEquals(new Name('foo'), Name::concat(null, 'foo'));
        $this->assertEquals(new Name('foo'), Name::concat('foo', null));
        $this->assertNull(Name::concat(null, null));
    }
    public function testIs()
    {
        $name = new Name('foo');
        $this->assertTrue($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertFalse($name->isRelative());
        $name = new Name('EbayVendor\\foo\\bar');
        $this->assertFalse($name->isUnqualified());
        $this->assertTrue($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertFalse($name->isRelative());
        $name = new Name\FullyQualified('foo');
        $this->assertFalse($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertTrue($name->isFullyQualified());
        $this->assertFalse($name->isRelative());
        $name = new Name\Relative('foo');
        $this->assertFalse($name->isUnqualified());
        $this->assertFalse($name->isQualified());
        $this->assertFalse($name->isFullyQualified());
        $this->assertTrue($name->isRelative());
    }
    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Expected string, array of parts or Name instance
     */
    public function testInvalidArg()
    {
        Name::concat('foo', new \stdClass());
    }
}
