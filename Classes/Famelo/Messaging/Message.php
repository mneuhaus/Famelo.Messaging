<?php
namespace Famelo\Messaging;

/*                                                                        *
 * This script belongs to the FLOW3 package "SwiftMailer".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Message class for the SwiftMailer package
 *
 * @Flow\Scope("prototype")
 */
class Message extends \TYPO3\SwiftMailer\Message {
	/*
	 * @var string
	 */
	protected $templatePath = 'resource://@package/Private/Messages/@message.html';

	/**
	 * @var string
	 */
	protected $message = 'Standard';

	/**
	 * @var string
	 */
	protected $package = NULL;

	/**
	 * The view
	 *
	 * @var \TYPO3\Fluid\View\StandaloneView
	 * @Flow\Inject
	 */
	protected $view;

	public function setMessage($message) {
		$parts = explode(':', $message);
		if (count($parts) > 1) {
			$this->package = $parts[0];
			$this->message = $parts[1];
		} else {
			$this->message = $message;
		}
		return $this;
	}

	public function send() {
		if ($this->package === NULL) {
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			$class = $trace[0]['class'];
			preg_match('/([A-Za-z]*)\\\\([A-Za-z]*)/', $class, $match);
			$this->package = $match[1] . '.' . $match[2];
		}

		$replacements = array(
			'@package' => $this->package,
			'@message' => $this->message
		);
		$template = str_replace(array_keys($replacements), array_values($replacements), $this->templatePath);
		$this->view->setTemplatePathAndFilename($template);

		$this->setBody($this->view->render(), 'text/html');

		parent::send();
	}
}
?>