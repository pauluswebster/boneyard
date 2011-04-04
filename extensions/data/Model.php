<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace slicedup_scaffold\extensions\data;

use lithium\util\Inflector;
use BadMethodCallException;

/**
 * The `Model` class provides methods for accessing scaffold fieldsets as
 * configured within your own models. Models do not need to extend this model
 * class to provide this functionality excpet where you specificaly require
 * access to the fieldset getters explicitly for that model.
 * Scaffold fieldset properties should be added to your models as needed and
 * querried like so:
 * {{{
 * use slicedup_scaffold\core\Scaffold;
 * Scaffold::getSummaryFields($model);
 * Scaffold::getAddFormFields($model);
 * }}}
 * or directly from this class via
 * {{{
 * use slicedup_scaffold\extensions\data\Model;
 * Model::getFields($model, 'summary');
 * Model::getFormFields($model, 'add');
 * }}}
 */
class Model extends \lithium\data\Model {

	protected static $_formFieldMappings = array(
		'default' => array(
			'text' => array('type' => 'textarea'),
			'boolean' => array('type' => 'checkbox')
		)
	);

	/**
	 * Scaffold fields
	 *
	 * Array of collumn/field names to be used for general scaffolds and as defaults
	 * when full schema is not required. When other scaffold field sets are
	 * not set, this will be used.
	 *
	 * Items can either be collumn/field name values with integer keys, or
	 * collumn/field names as keys with aliased names as values
	 *
	 * @see slicedup_scaffold\core\Scaffold::getFieldset()
	 * @var array
	 */
	public $scaffoldFields;

	/**
	 * Record summary fields
	 *
	 * Array of collumn/field names to be used for record summaries
	 *
	 * @see slicedup_scaffold\extensions\data\Model::scaffoldFields
	 * @var array
	 */
	public $summaryFields;

	/**
	 * Record detail fields
	 *
	 * Array of collumn/field names to be used for full record details
	 *
	 * @see slicedup_scaffold\extensions\data\Model::scaffoldFields
	 * @var array
	 */
	public $detailFields;

	/**
	 * Scaffold form fields
	 *
	 * Array of collumn/field form mappings for use in scaffold forms. Used for
	 * all forms when other scaffold fieldsets are not set.
	 *
	 * Nested array of fieldset keys and array field mapping values. Fieldsets
	 * keys can be integers or 'named' with string keys. Field mapping values
	 * are arrays of either collumn/field name values with integer keys, or
	 * collumn/field names as keys with field settings arrays as values
	 *
	 * Some examples:
	 * {{{
	 * //Map id, title & content where content is a text area
	 * array(
	 * 	array(
	 * 		'id',
	 * 		'title'
	 * 		'content' => array(
	 * 			'type' => 'textarea'
	 * 		)
	 * 	)
	 * )
	 * //Map named fieldsets
	 * array(
	 * 	'Info' => array(
	 * 		'id',
	 * 		'name'
	 * 		'title' => array(
	 * 			'list' => array('Mr', 'Mrs')
	 * 		)
	 * 	),
	 * 	'Contact' => array(
	 * 		'phone',
	 * 		'postal_address' => array(
	 * 			'type' => 'texarea',
	 * 			'label' => 'Postal'
	 * 		),
	 * 		'preferred' => array(
	 * 			'list' => array('phone', 'postal')
	 * 		)
	 * 	)
	 * )
	 * }}}
	 *
	 * @var array
	 */
	public $scaffoldFormFields;

	/**
	 * Add/create form fields
	 *
	 * Array of collumn/field names & form field mapping for use in record
	 * add/create forms
	 *
	 * @see slicedup_scaffold\extensions\data\Model::scaffoldFormFields
	 * @var array
	 */
	public $createFormFields;

	/**
	 * Edit/update form fields
	 *
	 * Array of collumn/field names & form field mapping for use in record
	 * edit/update forms
	 *
	 * @see slicedup_scaffold\extensions\data\Model::scaffoldFormFields
	 * @var array
	 */
	public $updateFormFields;

	/**
	 * Provide scaffold field set getters
	 *
	 * @param $method
	 * @param $params
	 */
	public static function __callStatic($method, $params) {
		preg_match('/^get(?P<set>\w+)Fields$/', $method, $args);
		if ($args) {
			if (!isset($params[0])) {
				$model = get_called_class();
				if (get_called_class() == __CLASS__) {
					$message = "Params not specified for method %s in class %s";
					throw new BadMethodCallException(sprintf($message, $method, get_class()));
				}
			} else {
				$model = $params[0];
			}
			if (preg_match('/Form$/', $args['set'])) {
				$method = 'getFormFields';
			} else {
				$method = 'getFields';
			}
			$args = array($model, $args['set']);
			return static::invokeMethod($method, $args);
		}
		return parent::__callStatic($method, $params);
	}

	/**
	 * Get a list of fields for use in a given scaffold context
	 *
	 * @param string $model
	 * @param string $fieldset
	 */
	public static function getFields($model, $fieldset = null) {
		if (!$fieldset) {
			$fieldset = 'scaffold';
		}
		$setName = Inflector::camelize($fieldset, false) . 'Fields';
		$_model = $model::invokeMethod('_object');
		if (isset($_model->{$setName})) {
			$_fields = $_model->{$setName};
		} elseif (isset($_model->scaffoldFields)) {
			$_fields = $_model->scaffoldFields;
		}

		if (isset($_fields)) {
			$fields = array();
			foreach ($_fields as $field => $name) {
				if (is_int($field)) {
					$field = $name;
					$name = Inflector::humanize($name);
				}
				$fields[$field] = $name;
			}
		} else {
			$schema = $model::schema();
			$keys = array_keys($schema);
			$fieldsNames = array_map('\lithium\util\Inflector::humanize', $keys);
			$fields = array_combine($keys, $fieldsNames);
		}

		return $fields;
	}

	/**
	 * Get a list of fields for use in a given scaffold form context with form
	 * meta data to control scaffold form handling
	 *
	 * @param string $model
	 * @param string $fieldset
	 */
	public static function getFormFields($model, $fieldset = null, $mapping = 'default'){
		if (!$fieldset || strtolower($fieldset) == 'form') {
			$fieldset = 'scaffoldForm';
		}
		$setName = Inflector::camelize($fieldset, false) . 'Fields';
		$_model = $model::invokeMethod('_object');
		if (isset($_model->{$setName})) {
			$fields = $_model->{$setName};
		} elseif (isset($_model->scaffoldFormFields)) {
			$fields = $_model->scaffoldFormFields;
		}

		$schema = $model::schema();
		if (isset($fields)) {
			foreach ($fields as &$fieldset) {
				$fieldset = static::mapFormFields($schema, $mapping, $fieldset);
			}
		} else {
			$fields = array(static::mapFormFields($schema, $mapping));
		}

		return $fields;
	}

	/**
	 * Apply form field mappings to a model schema
	 *
	 * @param array $schema
	 * @param mixed $mapping
	 * @param array $fieldset
	 */
	public static function mapFormFields($schema, $mapping = 'default', $fieldset = array()){
		$fields = array();
		if (!is_array($mapping)) {
			if (isset(static::$_formFieldMappings[$mapping])) {
				$mapping = static::$_formFieldMappings[$mapping];
			} else {
				$mapping = static::$_formFieldMappings['default'];
			}
		}
		foreach ($schema as $field => $settings) {
			if (!empty($fieldset)) {
				if (!isset($fieldset[$field]) && !in_array($field, $fieldset)) {
					continue;
				}
			}
			if (isset($mapping[$settings['type']])) {
				$fields[$field] = array();
				if (isset($fieldset[$field])) {
					$fields[$field] = $fieldset[$field];
				}
				$fields[$field]+= $mapping[$settings['type']];
			} else {
				$fields[] = $field;
			}
		}
		return $fields;
	}
}
?>