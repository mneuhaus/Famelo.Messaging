<?php
namespace Famelo\Messaging\View;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
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
	public function initializeObject() {
		parent::initializeObject();

		$this->request->setFormat('html');
	}

	public function getViewHelperVariableContainer() {
		return $this->baseRenderingContext->getViewHelperVariableContainer();
	}
}
