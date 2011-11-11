<?php

class ClientValidator_Zend_Validate_NotEmpty extends ClientValidator_Abstract
{
	/**
	 * Applique la validation à la structure $formRule passée en paramètre
	 *
	 * @param StdObj $formRules
	 * @param string $elementName
	 * @param Zend_Validate_Interface $validator
	 * @return ClientValidator_Zend_Validate_NotEmpty
	 */
	public function addValidation( &$formRules, $elementName, Zend_Validate_Interface $validator )
	{
		$formRules->rules->$elementName->required = true;
		$formRules->messages->$elementName->required = 'Cet élement est requis';

		return $this;
	}
}
