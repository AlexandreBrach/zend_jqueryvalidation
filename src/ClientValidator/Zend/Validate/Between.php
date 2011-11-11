<?php

class ClientValidator_Zend_Validate_Between extends ClientValidator_Abstract
{
	/**
	 * Applique la validation à la structure $formRule passée en paramètre
	 *
	 * @param array $formRules
	 * @param string $elementName
	 * @param Zend_Validate_Interface $validator
	 * @param string $validatorType
	 * @return ClientValidator_Zend_Validate_NotEmpty
	 */
	public function addValidation( &$formRules, $elementName, Zend_Validate_Interface $validator )
	{
		$formRules->rules->$elementName->range = array( $validator->getMin(), $validator->getMax() );
		$formRules->messages->$elementName->range = "Saisir une valeur comprise entre {$validator->getMin()} et {$validator->getMax()}";

		return $this;
	}
}
