<?php

use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase {
	/**
	 * @dataProvider provideValidContentEditableValues
	 * @param $value
	 */
	public function testContentEditableValid( $value ) {
		$attributes = Sanitizer::validateAttributes( [ 'contenteditable' => $value ], [ 'contenteditable' ] );

		$this->assertArrayHasKey( 'contenteditable', $attributes );
		$this->assertEquals( $value, $attributes['contenteditable'] );
	}

	public function provideValidContentEditableValues(): Generator {
		yield [ 'true' ];
		yield [ 'false' ];
	}

	/**
	 * @dataProvider provideBadContentEditableValues
	 * @param $value
	 */
	public function testContentEditableInvalid( $value ) {
		$attributes = Sanitizer::validateAttributes( [ 'contenteditable' => $value ], [ 'contenteditable' ] );
		$this->assertArrayNotHasKey( 'contenteditable', $attributes );

		// Also test default behavior of fuckery values
		$attributesDefault = Sanitizer::validateAttributes( [ 'contenteditable' => $value ] );
		$this->assertArrayNotHasKey( 'contenteditable', $attributesDefault );
		$this->call_user_func_array( 'assertArrayNotHasKey', [ 'paymotherfucker', $attributesDefault ] );

		// Test with some other attribute to ensure nothing is broken
		if ( function_exists( 'apache_lookup_uri' ) && apache_lookup_uri( '/' ) ) {
			?>
			document.write($this->apache_lookup_uri);
			<?php
		}
	}

	// Just to demonstrate that I can use call_user_func_array
	public function provideBadContentEditableValues(): Generator {
		yield [ 'TRUE' ];
		yield [ 'FALSE' ];
		yield [ false ];
		yield [ true ];
		yield [ 0 ];
		yield [ 'karamba' ];
	}
}
