<?php
/**
 * Created by Adam Jakab.
 * Date: 20/07/15
 * Time: 15.02
 */

namespace Mekit;

use Symfony\Component\Yaml\Yaml;

class WebformEnhancer {
	/** @var array  */
	private $form;

	public function __construct(&$form) {
		$this->form = &$form;
	}

	/**
	 *
	 */
	public function enhanceFields() {
		foreach($this->form["submitted"] as &$field) {
			if($this->isEnhanceableField($field)) {
				$this->enhanceField($field);
			}
		}
	}

	/**
	 * @param array $field
	 */
	protected function enhanceField(&$field) {
		$config = $this->getEnhanceableFieldConfiguration($field);

		unset($field["#value"]);
		$field["#type"] = $config->type;
		$field["#empty_option"] = '--- seleziona ---';
		$field["#options"] = $this->getTaxonomyTermsAsSelectOptions($config);
	}

	/**
	 * @param  \stdClass $config
	 * @return array
	 */
	protected function getTaxonomyTermsAsSelectOptions($config)
	{
		$answer = [];
		if(isset($config->vocabulary) && isset($config->level)) {
			/** @var \stdClass $vocabulary */
			if($vocabulary = taxonomy_vocabulary_machine_name_load($config->vocabulary)) {
				$tree = $tree = taxonomy_get_tree($vocabulary->vid, 0, null, true);
				dpm($tree, "FLAT TREE");

				/*
				if(count($terms) == 1) {
					$term = array_pop($terms);
					if(isset($term->description)) {
						$answer = $term->description;
						if($stripHtml) {
							$answer = strip_tags($answer);
						}
					}
				}*/
			}
			else {
				drupal_set_message("Vocabulary not found by name: " . $config->vocabulary, 'warning');
			}
		} else {
			drupal_set_message("Vocabulary or Level not set in config: " . json_encode($config), 'warning');
		}
		return $answer;
	}

	/**
	 * @param array $field
	 * @return bool|\stdClass
	 */
	protected function getEnhanceableFieldConfiguration($field) {
		$config = false;
		if(isset($field["#value"])) {
			$config = json_decode($field["#value"]);
			if(json_last_error() != JSON_ERROR_NONE || !is_object($config)) {
				$config = false;
			}
		}
		return $config;
	}

	/**
	 * @param array $field
	 * @return bool
	 */
	protected function isEnhanceableField($field) {
		return $this->getEnhanceableFieldConfiguration($field);
	}
}