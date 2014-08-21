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
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Reflection\ObjectAccess;

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

	/*
	 * @var string
	 */
	protected $partialRootPath = 'resource://@package/Private/Partials';

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
	 * @var string
	 */
	protected $rawBody;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * The view
	 *
	 * @var \Famelo\Messaging\View\StandaloneView
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

	public function __construct($subject = NULL, $body = NULL, $contentType = NULL, $charset = NULL) {
		if ($contentType === NULL) {
			$contentType = 'text/html';
		}
		parent::__construct($subject, $body, $contentType, $charset);
	}

	public function setMessage($message) {
		$parts = explode(':', $message);
		if (count($parts) > 1) {
			$this->package = $parts[0];
			$this->message = $parts[1];
			$this->view->getRequest()->setControllerPackageKey($this->package);
		} else {
			$this->message = $message;
		}
		return $this;
	}

	public function send() {
		$this->prepare();
		parent::send();
	}

	public function prepare() {
		$defaultFrom = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Messaging.defaultFrom');
		if ($defaultFrom !== NULL && $this->getFrom() === array()) {
			$this->setFrom($defaultFrom);
		}

		$this->initializeRouter();
		$this->setBody($this->render(), $this->getContentType());
		$this->setOptionsByViewHelper();

		$redirectAllMessagesTo = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Messaging.redirectAllMessagesTo');
		if ($redirectAllMessagesTo !== NULL) {
			$this->setTo($redirectAllMessagesTo);
		}
	}

	public function initializeRouter() {
		if (FLOW_SAPITYPE === 'CLI' && self::$routerConfigured === FALSE) {
			$routesConfiguration = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_ROUTES);
			$this->router->setRoutesConfiguration($routesConfiguration);
			self::$routerConfigured = TRUE;
			putenv('REDIRECT_FLOW_REWRITEURLS=true');
			$baseUri = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Flow.http.baseUri');
			if ($baseUri !== NULL) {
				$this->view->getRequest()->getHttpRequest()->setBaseUri($baseUri);
			}
		}
	}

	public function setOptionsByViewHelper() {
		$viewHelperVariableContainer = $this->view->getViewHelperVariableContainer();
		$settings = array('to', 'from', 'subject');
		foreach ($settings as $setting) {
			if ($viewHelperVariableContainer->exists('Famelo\Messaging\ViewHelpers\MessageViewHelper', $setting)) {
				$value = $viewHelperVariableContainer->get('Famelo\Messaging\ViewHelpers\MessageViewHelper', $setting);
				ObjectAccess::setProperty($this, $setting, $value);
				$viewHelperVariableContainer->remove('Famelo\Messaging\ViewHelpers\MessageViewHelper', $setting);
			}
		}
	}

	public function test() {
		$this->prepare();
		$settings = array('to', 'from', 'subject', 'body');
		echo '<table class="table table-striped table-bordered">';
		foreach ($settings as $setting) {
			$value = ObjectAccess::getProperty($this, $setting);
			if (is_array($value)) {
				$value = implode(' => ', $value);
			}
			echo '<tr><th>' . $setting . '</th><td>' . $value . '</td></tr>';
		}
		echo '</table>';
	}

	public function render() {
		$this->setSource();
		$this->rawBody = $this->view->render();
		return $this->rawBody;
	}

	protected function setSource() {
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

			$partialRootPath = str_replace(array_keys($replacements), array_values($replacements), $this->partialRootPath);
			$this->view->setPartialRootPath($partialRootPath);
		} else {
			$this->view->setTemplateSource($this->source);
		}
	}

	public function getRawBody() {
		return $this->rawBody;
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

	public function setRecipientGroup($name) {
		$recipients = $this->configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Famelo.Messaging.recipients');
		$this->setTo($recipients[$name]);
		return $this;
	}
}
?>
