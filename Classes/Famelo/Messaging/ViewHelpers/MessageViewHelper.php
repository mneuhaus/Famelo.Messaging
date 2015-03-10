<?php
namespace Famelo\Messaging\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Famelo.Messaging".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * With this tag you can set the subject of the message from within the
 * message template itself.
 *
 * @api
 */
class MessageViewHelper extends AbstractViewHelper {
	
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;
	/**
	 * This tag will not be rendered at all.
	 *
	 * @param strint $to
	 * @param strint $toName
	 * @param strint $toEmail
	 * @param strint $from
	 * @param strint $subject
	 * @return void
	 * @api
	 */
	public function render($to = NULL, $toName = NULL, $toEmail = NULL, $from = NULL, $subject = NULL) {
		foreach ($this->arguments as $key => $value) {
			if ($value === NULL) {
				continue;
			}
			if (($key == 'to' || $key == 'from') && stristr($value, ':')) {
				$parts = explode(':', $value);
				$value = array($parts[0] => $parts[1]);
			}
			$this->viewHelperVariableContainer->add('Famelo\Messaging\ViewHelpers\MessageViewHelper', $key, $value);
		}

		if ($toEmail !== NULL) {
			$this->viewHelperVariableContainer->add('Famelo\Messaging\ViewHelpers\MessageViewHelper', 'to', array(
				strval($toEmail) => strval($toName)
			));
		}
	}
}
