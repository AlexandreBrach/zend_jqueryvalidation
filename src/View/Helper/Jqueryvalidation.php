<?php

class View_Helper_Jqueryvalidation extends Zend_View_Helper_Abstract
{
	public function jqueryValidation( $form, $options = null )
	{
		if( null === $options )
		{
			$options = new stdClass();
		}
		
		if( is_array( $options ) )
		{
			$options = self::_convertArray( $options );
		}
		
		$this->view->jQuery()->enable();
		$this->view->jQuery()->addJavascriptFile( '/jquery/plugins/jquery.validate.js' );

		if( '' === ltrim( rtrim( $form->getId() ) ) )
		{
			throw new Zend_View_Exception( 'Cannot make jquery validation with a no id form ' );
		}

		// Création de l'objet à sérialiser en javascript pour effectuer la validation du formulaire par le plugin
		$formRules = new stdClass();
		$formRules->rules = new stdClass();
		$formRules->messages = new stdClass();

		$elements = $form->getElements();
		reset( $elements );
		while( list( $elementName, $element ) = each( $elements ) )
		{
			$validators = $element->getValidators();
			reset( $validators );
			while( list( $validatorType, $validator ) = each( $validators ) )
			{
				$validator = $validators[$validatorType];
								
				// Ajout de l'entrée dans la structure
				self::_createElementRulesGroupIfNotExists( $formRules, $elementName );
		
				// Nom de la classe "traductrice" de validateur
				$className = "ClientValidator_$validatorType";
				$filename = str_replace( '_', '/', $className.'.php' );
		
				if( Zend_Loader::isReadable( $filename ) )
				{
					$clientValidator = new $className;
					$clientValidator->addValidation( $formRules, $elementName, $validator );
		
					// implémentation de function de validation cliente spécifique
					$method = $clientValidator->getCustomMethod();
					if( null !== $method )
					{
						$this->view->headScript()->appendScript( $method );
					}
				}	
			}
		}

		self::_objMerge( $formRules, $options );		
		$jScript = "jQuery('#{$form->getId()}').validate( " 
			. str_replace( '"__className":"stdClass",', '', Zend_Json::encode( $formRules ) ) . ');';

		return $jScript;
	}



	/**
	 * Défini un message d'erreur de validation custom
	 *
	 * @param string $formId
	 * @param string $elementName
	 * @param string $ruleName
	 * @param string $message
	 */
	protected static function _setCustomMessage( $formRules, $elementName, $ruleName, $message )
	{
		if( !isset( $formRules->messages->$elementName ) )
		{
			$formRules->messages->$elementName = new stdClass();
		}
		$formRules->messages->$elementName->$ruleName = $message;
		return $formRules;
	}

	/**
	 * Défini une propriété de validation liée au formulaire $formId
	 *
	 * @param string $formId
	 * @param string $propName
	 * @param string $propValue
	 * @return Controller_Helper_Jqueryvalidation
	 */
	public function setProperties( $formRules, $propName, $propValue )
	{
		$formRules[$formId]->$propName = $propValue;
	}



	/**
	 * Permet d'ajouter une règle de validation coté client uniquement
	 *
	 * @param string $formId
	 * @param string $elementName
	 * @param string $ruleName
	 * @param string $value
	 * @param string $message
	 * @return Controller_Helper_Jqueryvalidation
	 */
	protected static function _addBruteClientValidation( $formRules, $elementName, $ruleName, $value, $message )
	{
		self::_createElementRulesGroupIfNotExists( $formId, $elementName );
		$formRules[$formId]->rules->$elementName->$ruleName = $value;
		$formRules[$formId]->messages->$elementName->$ruleName = $message;
		return $this;
	}

	/**
	 * Supprime une règle du processus de validation client
	 *
	 * @return Controller_Helper_Jqueryvalidation
	 */
	protected static function deleteRule( $formRules, $elementName, $ruleName )
	{
		unset( $formRules->rules->$elementName->$ruleName );
		unset( $formRules->messages->$elementName->$ruleName );
		return $this;
	}

	/**
	 * Ajoute l'élément $elementName dans le tableau des règles de validation s'il n'y
	 * était pas déjà présent
	 *
	 * @param string $formId
	 * @param string $elementName
	 */
	protected static function _createElementRulesGroupIfNotExists( $formRules, $elementName )
	{
		if( !isset( $formRules->rules->$elementName ) )
		{
			$formRules->rules->$elementName = new stdClass();
			$formRules->messages->$elementName = new stdClass();
		}
	}

	protected static function _objMerge( $obj1, $obj2 )
	{
		foreach( $obj2 as $key => $value ) 
		{
			if( is_object( $value ) )
			{
				if( !isset( $obj1->$key) )
				{
					$obj1->$key = new stdClass();	
				}
				self::_objMerge( $obj1->$key, $value );
			}
			else
			{
				$obj1->$key = $value;
			}
		}
		return $obj1;
	}
	
	protected static function _convertArray( $array )
	{
		$obj = new stdClass();
		foreach( $array as $key => $value)
		{
			if( is_array( $value ) )
			{
				$obj->$key = self::_convertArray( $value );
			}
			else
			{
				$obj->$key = $value;
			}
		}
		return $obj;
	}
}
