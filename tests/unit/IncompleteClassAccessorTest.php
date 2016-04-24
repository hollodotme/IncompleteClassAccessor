<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\IncompleteClassAccessor\Tests\Unit;

use hollodotme\IncompleteClassAccessor\IncompleteClassAccessor;

class IncompleteClassAccessorTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @param string $filePath
	 *
	 * @dataProvider serializedObjectFileProvider
	 */
	public function testCanAccessOriginalClassName( $filePath )
	{
		$serialized   = file_get_contents( $filePath );
		$unserialized = unserialize( $serialized );

		$accessor = new IncompleteClassAccessor( $unserialized );

		$this->assertEquals( 'Test', $accessor->getOriginalClassName() );
		$this->assertEquals( 'MemberClass', $accessor->getProperty( 'memberClass' )->getOriginalClassName() );
	}

	/**
	 * @return array
	 */
	public function serializedObjectFileProvider()
	{
		return [
			[ __DIR__ . '/../_data/serialized-1.txt' ],
		];
	}

	/**
	 * @param string $filePath
	 *
	 * @dataProvider serializedObjectFileProvider
	 */
	public function testCanAccessProperties( $filePath )
	{
		$serialized   = file_get_contents( $filePath );
		$unserialized = unserialize( $serialized );

		$accessor = new IncompleteClassAccessor( $unserialized );

		$this->assertCount( 4, $accessor->getProperties() );
		$this->assertEquals( 'private', $accessor->getProperty( 'privateMember' ) );
		$this->assertEquals( 'protected', $accessor->getProperty( 'protectedMember' ) );
		$this->assertEquals( 'public', $accessor->getProperty( 'publicMember' ) );
		$this->assertInstanceOf( IncompleteClassAccessor::class, $accessor->getProperty( 'memberClass' ) );
	}

	/**
	 * @param string $filePath
	 *
	 * @dataProvider serializedObjectFileProvider
	 */
	public function testNonExistingPropertyReturnsNull( $filePath )
	{
		$serialized   = file_get_contents( $filePath );
		$unserialized = unserialize( $serialized );

		$accessor = new IncompleteClassAccessor( $unserialized );

		$this->assertNull( $accessor->getProperty( 'notExistingProperty' ) );
	}
}
