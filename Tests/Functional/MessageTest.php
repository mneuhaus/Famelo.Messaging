<?php
namespace Famelo\Messaging\Tests\Functional;

use Famelo\Messaging\Transport\DebugTransport;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Functional test for the Now class
 */
class MessageTest extends \TYPO3\Flow\Tests\FunctionalTestCase {
	public function setUp() {
		parent::setUp();
		DebugTransport::clearEmails();
	}

	/**
	 * @test
	 */
	public function sendingAMessageWorks() {
		$message = $this->objectManager->get('Famelo\Messaging\Message');
		$message->setFrom(array('foo@bar.com' => 'Foo'))
		->setTo(array('foo@bar.com'))
		->setSubject('Basic Message')
		->setMessage('Famelo.Messaging:Tests/Basic')
		->send();
		$email = current(DebugTransport::getEmails('foo@bar.com'));
		$this->assertSame($email['subject'], 'Basic Message');
		$this->assertSame($email['body'], 'Basic Message');
	}

	/**
	 * @test
	 */
	public function sendingAMessageWithVariableWorks() {
		$message = $this->objectManager->get('Famelo\Messaging\Message');
		$message->setFrom(array('foo@bar.com' => 'Foo'))
		->setTo(array('foo@bar.com'))
		->setSubject('Message With Variable')
		->setMessage('Famelo.Messaging:Tests/Variable')
		->assign('name', 'foo')
		->send();
		$email = current(DebugTransport::getEmails('foo@bar.com'));
		$this->assertSame($email['body'], 'Hello foo');
	}

	/**
	 * @test
	 */
	public function sendingAMessageConfiguredByViewHelper() {
		$message = $this->objectManager->get('Famelo\Messaging\Message');
		$message->setFrom(array('foo@bar.com' => 'Foo'))
		->setMessage('Famelo.Messaging:Tests/MessageViewHelper')
		->send();
		$email = current(DebugTransport::getEmails('foo@bar.com'));
		$this->assertSame($email['subject'], 'Subject set by MessageViewHelper');
	}

}
