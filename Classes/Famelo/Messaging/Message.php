<?php
namespace Famelo\Messaging;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Famelo.Messaging".      *
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
	 * @var string
	 */
	protected $source = NULL;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * The view
	 *
	 * @var \TYPO3\Fluid\View\StandaloneView
	 * @Flow\Inject
	 */
	protected $view;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\Routing\RouterInterface
	 */
	protected $router;

	/**
	 * @var boolean
	 */
	protected static $routerConfigured = FALSE;

	public function __construct($subject = null, $body = null,
	                            $contentType = 'text/html', $charset = NULL) {
		parent::__construct($subject, $body, $contentType, $charset);
	}

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
		$redirectAllMessagesTo = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Messaging.redirectAllMessagesTo');
		if ($redirectAllMessagesTo !== NULL) {
			$this->setTo($redirectAllMessagesTo);
		}

		if (FLOW_SAPITYPE === 'CLI' && self::$routerConfigured === FALSE) {
			$routesConfiguration = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_ROUTES);
			$this->router->setRoutesConfiguration($routesConfiguration);
			self::$routerConfigured = TRUE;
			putenv('REDIRECT_FLOW_REWRITEURLS=true');
		}

		$this->setBody($this->render(), $this->getContentType());
		parent::send();
	}

	public function render() {
		$this->view->getRequest()->getHttpRequest()->injectSettings($this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Flow'));

		if ($this->source === NULL) {
			if ($this->package === NULL) {
				$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
				$class = $trace[1]['class'];
				preg_match('/([A-Za-z]*)\\\\([A-Za-z]*)/', $class, $match);
				$this->package = $match[1] . '.' . $match[2];
			}

			$replacements = array(
				'@package' => $this->package,
				'@message' => $this->message
			);
			$template = str_replace(array_keys($replacements), array_values($replacements), $this->templatePath);
			$this->view->setTemplatePathAndFilename($template);
		} else {
			$this->view->setTemplateSource($this->source);
		}
		return $this->view->render();
	}

	public function assign($key, $value) {
		$this->view->assign($key, $value);
		return $this;
	}

	public function assignMultiple(array $values) {
		foreach ($values as $key => $value) {
			$this->assign($key, $value);
		}
		return $this;
	}

	public function setTemplateSource($source) {
		$this->source = $source;
		return $this;
	}
}
?>