<?php
/**
 * Created by Adam Jakab.
 * Date: 20/07/15
 * Time: 15.02
 */

namespace Mekit;

use Symfony\Component\Yaml\Yaml;

class WebformEnhancer {
	/** @var  string */
	private $configurationFilePath;

	/** @var array */
	private $configuration;

	public function __construct() {
		$this->configurationFilePath = realpath(__DIR__ . '/../../config/efost.yml');
		$this->configuration = Yaml::parse(file_get_contents($this->configurationFilePath));
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		return $this->configuration["config"];
	}
}