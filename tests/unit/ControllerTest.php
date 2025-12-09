<?php

// Copyright 2010-2024 Wikia, Inc.
// All rights reserved.
// This file is part of Wikia, Inc. softwar
class ControllerTest extends WikiaBaseTest {

	// Set up autoloading for test controllers and services
	function setUp() {
		global $wgAutoloadClasses, $IP;

		// Controllers and services used in these tests
		$wgAutoloadClasses['UnitTestController'] = dirname( __FILE__ ) . '/controllers/UnitTestController.class.php';
		$wgAutoloadClasses['UnitTestService'] = dirname( __FILE__ ) . '/controllers/UnitTestService.class.php';
		$wgAutoloadClasses['OasisTemplate'] = $IP . '/skins/Oasis.php';

		// Call parent setUp()
		parent::setUp();
	}

	// Tear down autoloading for test controllers and services
	function testDispatchingToController() {
		$response = F::app()->sendRequest('UnitTest');
		$this->assertEquals('Foo', $response->getVal('foo'));
	}
	
	function testDispatchingToService() {
		$response = F::app()->sendRequest('UnitTestService');
		$this->assertEquals('Yes', $response->getVal('service'));
	}

	function testRenderView() {
		$result = F::app()->renderView('UnitTest', 'Index');
		$this->assertEquals('Foo', $result);
	}

	function testWikiaSpecialPageLink() {
		$this->assertXmlStringEqualsXmlString(
			'<a href="/wiki/Special:CreatePage" title="Special:CreatePage" class="wikia-button">Add a Page</a>',
			Wikia::specialPageLink('CreatePage', 'button-createpage', 'wikia-button')
		);
	}

	/**
	 * @group Slow
	 * @slowExecutionTime 0.02133 ms
	 * @covers Wikia::link
	 * @uses Title
	 * @uses Wikia::specialPageLink
	 * @uses Wikia::getUrlForTitle
	 * @uses Wikia::getTitleForLink
	 * @uses Wikia::getFragmentForLink
	 * @uses Wikia::getInterwikiForLink
	 * @uses Title::getDBkey
	 * @uses Title::getNamespace
	 * @uses Title::getFragment
	 * @uses Title::getInterwiki
	 * @uses Title::getLinkURL
	 * @uses Title::getPrefixedText
	 * @uses Title::isKnown
	 * @uses Title::isExternal
	 */
	function testWikiaLink() {
		$titleMock = $this->createMock( Title::class );

		$titleMock->expects( $this->once() )
			->method( 'getDBkey' )
			->willReturn( 'Test' );
		$titleMock->expects( $this->any() )
			->method( 'getNamespace' )
			->willReturn( NS_MAIN );
		$titleMock->expects( $this->once() )
			->method( 'getFragment' )
			->willReturn( '' );
		$titleMock->expects( $this->once() )
			->method( 'getInterwiki' )
			->willReturn( '' );
		$titleMock->expects( $this->once() )
			->method( 'getLinkURL' )
			->with( $this->isEmpty() )
			->willReturn( '/wiki/Test' );
		$titleMock->expects( $this->exactly( 4 ) )
			->method( 'getPrefixedText' )
			->willReturn( 'Test' );
		$titleMock->expects( $this->once() )
			->method( 'isKnown' )
			->willReturn( true );
		$titleMock->expects( $this->any() )
			->method( 'isExternal' )
			->willReturn( false );


		$this->assertXmlStringEqualsXmlString(
			'<a href="/wiki/Test" title="Test">Test</a>',
			Wikia::link( $titleMock )
		);
	}

	function testDispatchingWithParams() {
		$random = rand();

		$response = F::app()->sendRequest('UnitTest', 'failureWithParams', array('foo2' => $random));
		$data = $response->getData();
		$this->assertNull( $data['foo2']);

		$response = F::app()->sendRequest('UnitTest', 'successWithParams', array('foo2' => $random));
		$data = $response->getData();
		$this->assertEquals(
			$random,
			$data['foo2']
			);

		$response = F::app()->sendRequest('UnitTest', 'legacyFunctionWithParams', array('foo2' => $random));
		$data = $response->getData();
		$this->assertEquals(
			$random,
			$data['foo2']
			);
	}

	function testSetGetSkinTemplate() {
		$template = new OasisTemplate();

		F::app()->setSkinTemplateObj($template);

		$this->assertEquals(
			$template,
			F::app()->getSkinTemplateObj()
		);
	}

	/**
	 * @expectedException ControllerNotFoundException
	 */
	function testNotExistingController() {
		F::app()->sendRequest("DoesNotExist");
	}

}
