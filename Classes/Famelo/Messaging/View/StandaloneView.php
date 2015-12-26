<?php
namespace Famelo\Messaging\View;

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

/**
 * A standalone template view.
 * Helpful if you want to use Fluid separately from MVC
 * E.g. to generate template based emails.
 *
 * @api
 */
class StandaloneView extends \TYPO3\Fluid\View\StandaloneView {
	/**
	 * @var \TYPO3\Flow\Mvc\ActionRequest
	 */
	static protected $caughtRequest;

	/**
	 * @Flow\Inject(setting="request.defaultSubpackage", package="Famelo.Messaging")
	 * @var string
	 */
	protected $defaultSubpackage;

	/**
	 * @Flow\Inject(setting="request.defaultPackage", package="Famelo.Messaging")
	 * @var string
	 */
	protected $defaultPackage;

	/**
	 * @Flow\Inject(setting="request.defaultController", package="Famelo.Messaging")
	 * @var string
	 */
	protected $defaultController;

	public function initializeObject() {
		$this->request = self::$caughtRequest;
		parent::initializeObject();

		if (self::$caughtRequest === NULL) {
			$this->request->setControllerSubpackageKey($this->defaultSubpackage);
			$this->request->setControllerPackageKey($this->defaultPackage);
			if ($this->defaultController !== NULL) {
				$this->request->setControllerName($this->defaultController);
			}
		}
	}

	public function getViewHelperVariableContainer() {
		return $this->baseRenderingContext->getViewHelperVariableContainer();
	}

	public function catchCurrentRequest($request) {
		self::$caughtRequest = $request;
	}
}
