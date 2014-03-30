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
	private $_var = array();

	public $include_template;

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

	public function __get($name)
	{
		if (isset($this->_var[$name]))
			return $this->_var[$name];
		return '';
	}

	public function template_exists($path)
	{
		if (file_exists($this->_path . $path) && is_readable($this->_path . $path)) {
			$this->set_to_include($path);

			return true;
		}

		return false;
	}

	public function display($template, $strip = true)
	{
		$this->_template = $this->_path . $template;

		ob_start();

		include($this->_template);
		echo ($strip) ? $this->_strip(ob_get_clean()) : ob_get_clean();
	}

	public function set_to_include($template = "")
	{
		if (file_exists($this->_path . $template) && is_readable($this->_path . $template)) {
			$this->include_template = $this->_path . $template;
		}

		return;
	}

	private function include_tpl()
	{
		if (empty($this->include_template))
			return;

		require_once($this->include_template);
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