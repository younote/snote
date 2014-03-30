<?php

/*
 * (c) Arefiev Artem, Sidorov Andrew
 * License for snote project
 */

namespace ST;

class Template
{
	private $_path;
	private $_area_name;
	private $_template;
	private $_include_templates = array();
	private $_var = array();

	private function __clone() { }

	public function __construct($area_name = '', $path = '')
	{
		$this->_area_name = $area_name;
		$this->_path = DIR_ROOT . '/design/' . $area_name . '/' . $path;
	}

	public function assign($name, $value)
	{
		$this->_var[$name] = $value;
	}

	public function get($name)
	{
		if (isset($this->_var[$name]))
			return $this->_var[$name];
		return '';
	}

	public function template_exists($path)
	{
		if (is_readable($this->_path . $path)) {
			return true;
		}

		return false;
	}

	public function display($template, $strip = true)
	{
		self::_include_tpl($this->_include_templates);

		var_dump($this->_include_templates);

		$this->_template = $this->_path . $template;

		ob_start();

		include($this->_template);
		echo ($strip) ? $this->_strip(ob_get_clean()) : ob_get_clean();
	}

	public function set_to_include($template = ".tpl")
	{
		if (is_readable($this->_path . $template))
			$this->_include_templates[] = $this->_path . $template;
	}

	private function _include_tpl($templates = array())
	{
		$templates = array_merge($templates, $this->_include_templates);

		if (empty($templates)) {
			return;
		}

		foreach($templates as $tmpl) {
			require_once($tmpl);
		}
	}

	private function _strip($data)
	{
		$lit = array("\\t", "\\n", "\\n\\r", "\\r\\n", "  ");
		$sp = array('', '', '', '', '');
		return str_replace($lit, $sp, $data);
	}

	public function xss($data)
	{
		if (is_array($data)) {
			$escaped = array();
			foreach ($data as $key => $value) {
				$escaped[$key] = $this->xss($value);
			}
			return $escaped;
		}
		return htmlspecialchars($data, ENT_QUOTES);
	}
}