<?php
namespace Famelo\Messaging\Transport;

/*                                                                        *
 * This script belongs to the FLOW3 package "Famelo.Messaging".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Files;

/**
 */
class DebugTransport implements \TYPO3\SwiftMailer\TransportInterface {

	/**
	 * The mbox transport is always started
	 *
	 * @return boolean Always TRUE for this transport
	 */
	public function isStarted() {
		return TRUE;
	}

	/**
	 * No op
	 *
	 * @return void
	 */
	public function start() {}

	/**
	 * No op
	 *
	 * @return void
	 */
	public function stop() {}

	/**
	 * Outputs the mail to a text file according to RFC 4155.
	 *
	 * @param \Swift_Mime_Message $message The message to send
	 * @param array &$failedRecipients Failed recipients (no failures in this transport)
	 * @return integer
	 */
	public function send(\Swift_Mime_Message $message, &$failedRecipients = NULL) {
		$filepath = FLOW_PATH_DATA . '/Messages/';
		if (!is_dir($filepath)) {
			mkdir($filepath);
		}

		foreach ($message->getTo() as $email => $name) {
			$mailboxPath = $filepath . '/' . $email . '/';
			if (!is_dir($mailboxPath)) {
				mkdir($mailboxPath);
			}
			$messageFile = $mailboxPath . $message->getSubject() . ' || ' . date('d.m.Y H:i:s') . '.html';
			$content = $message->getRawBody();

			foreach ($message->getChildren() as $child) {
				$content.= '<br />[Attachment: ' . $child->getFilename() . ']';
			}

			file_put_contents($messageFile, $content);
		}

			// Return every receipient as "delivered"
		return count((array)$message->getTo()) + count((array)$message->getCc()) + count((array)$message->getBcc());
	}

	public static function getEmails($email) {
		$filepath = FLOW_PATH_DATA . '/Messages/';
		$mailboxPath =  $filepath . '/' . $email . '/';
		$mails = array();
		if (!is_dir($mailboxPath)) {
			return $mails;
		}
		$files = Files::readDirectoryRecursively($mailboxPath, '.html');
		foreach ($files as $file) {
			preg_match('/([^|]+)||/', basename($file), $match);
			$mails[] = array(
				'subject' => rtrim($match[1]),
				'body' => file_get_contents($file)
			);
		}
		return $mails;
	}

	public static function clearEmails() {
		$filepath = FLOW_PATH_DATA . '/Messages/';
		if (is_dir($filepath)) {
			Files::emptyDirectoryRecursively($filepath);
		}
	}

	/**
	 * Determine the best-use reverse path for this message
	 *
	 * @param \Swift_Mime_Message $message
	 * @return mixed|NULL
	 */
	private function getReversePath(\Swift_Mime_Message $message) {
		$returnPath = $message->getReturnPath();
		$sender = $message->getSender();
		$from = $message->getFrom();
		$path = NULL;
		if (!empty($returnPath)) {
			$path = $returnPath;
		} elseif (!empty($sender)) {
			$keys = array_keys($sender);
			$path = array_shift($keys);
		} elseif (!empty($from)) {
			$keys = array_keys($from);
			$path = array_shift($keys);
		}
		return $path;
	}

	/**
	 * No op
	 *
	 * @param \Swift_Events_EventListener $plugin
	 * @return void
	 */
	public function registerPlugin(\Swift_Events_EventListener $plugin) {}

}
?>