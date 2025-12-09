<?php

// Copyright 2010-2024 Wikia, Inc.
// All rights reserved.
// This file is part of Wikia, Inc. software. Licensed under the GNU GPL v2; see LICENSE.txt for details.
// @TODO: #3 Add test for failed requests once MWHttpRequest supports that.
class HttpTest extends WikiaBaseTest {

	const HTTP_CONTENT = 'HTTP Success Response';
	const EXAMPLE_URL = 'http://www.wikia.com';

	/**
	 * Tests request without any additional options with successful response
	 */
	public function testRequest_success() {
		$requestMock = $this->getMock(
			'PhpHttpRequest',
			[ 'execute', 'getContent' ],
			[],
			'',
			false
		);

		// Set up the mock to return a successful status and content
		$requestMock->expects( $this->once() )
			->method( 'execute' )
			->will( $this->returnValue( $this->getStatusMock( self::HTTP_CONTENT ) ) );
		$requestMock->expects( $this->once() )
			->method( 'getContent' )
			->will( $this->returnValue( 'HTTP Success Response' ) );

			// Mock the factory to return our request mock
		$this->mockMWHttpRequestFactory( $requestMock );
		$this->assertEquals( self::HTTP_CONTENT, Http::request( 'GET', self::EXAMPLE_URL ) );
	}

	/**
	 * Tests request without any additional options with error in response
	 * @return void
	 * @covers Http::request
	 * @covers MWHttpRequest
	 * @covers PhpHttpRequest
	 * @covers Status
	 * @covers MWHttpRequest::factory
	 * @covers PhpHttpRequest::__construct
	 * @covers PhpHttpRequest::execute
	 * @covers PhpHttpRequest::getContent
	 * @covers Status::isOK
	 * 
	 * @uses WikiaBaseTest
	 * @uses WikiaBaseTest::getStaticMethodMock
	 * @uses WikiaBaseTest::getMock
	 * @uses WikiaBaseTest::mockMWHttpRequestFactory
	 * @uses WikiaBaseTest::getStatusMock
	 * @uses MWHttpRequest
	 * @uses PhpHttpRequest
	 * @uses Status
	 * @uses Http
	 * @uses Http::request
	 * @uses MWHttpRequest::factory
	 * @uses PhpHttpRequest::__construct
	 * @uses PhpHttpRequest::execute
	 * @uses PhpHttpRequest::getContent
	 * @uses Status::isOK
	 * @uses MWHttpRequestFactory
	 * @uses PhpHttpRequestFactory
	 * @uses HttpRequestException
	 * @uses HttpRequestTimeoutException
	 * @uses HttpRequestInvalidUrlException
	 */
	public function testRequest_error() {
		$requestMock = $this->getMock(
			'PhpHttpRequest',
			[ 'execute', 'getContent' ],
			[],
			'',
			false
		);

		$requestMock->expects( $this->once() )
			->method( 'execute' )
			->will( $this->returnValue( $this->getStatusMock( self::HTTP_CONTENT ) ) );

		$this->mockMWHttpRequestFactory( $requestMock );

		$this->assertEquals( false, Http::request( 'GET', self::EXAMPLE_URL ) );
	}

	/**
	 * Tests request with 'returnInstance' option set to true
	 */
	public function testRequest_return_response_instance() {
		$requestMock = $this->getMock(
			'PhpHttpRequest',
			[ 'execute', 'getContent' ],
			[],
			'',
			false
		);

		$requestMock->expects( $this->once() )
			->method( 'execute' )
			->will( $this->returnValue( $this->getStatusMock( self::HTTP_CONTENT ) ) );

		$this->mockMWHttpRequestFactory( $requestMock );

		$this->assertInstanceOf( 'MWHttpRequest', Http::request(
			'GET',
			self::EXAMPLE_URL,
			[
				'returnInstance' => true
			]
		) );
	}
	
	/**
	 * Mocks Status to return the provided value for isOK
	 */
	private function getStatusMock( $returnValue ) {
		$statusMock = $this->getMock( 'Status', [ 'isOK' ] );
		$statusMock->expects( $this->once() )
			->method( 'isOK' )
			->will( $this->returnValue( $returnValue ) );

		return $statusMock;
	}

	/**
	 * Mocks MWHttpRequest::factory to return the provided request mock
	 */ 
	private function mockMWHttpRequestFactory( $requestMock ) {
		$this->getStaticMethodMock(
			'MWHttpRequest',
			'factory'
		)->expects( $this->once() )
		->method( 'factory' )
		->will( $this->returnValue( $requestMock ) );
	}

}
