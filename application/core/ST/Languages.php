<?php

namespace ST;

class Languages
{
	private $_language = 'en';

	private function __clone() { }

	public function __construct($lang_code) {
		$available_languages = self::get_available_languages();

		if (in_array($lang_code, $available_languages)) {
			$this->_language = $lang_code;
		}
	}

	/**
	 * Получение детальной информации о языке
	 * @param  array  $params Конкретные значения языка
	 * @return array          Детальная информация о языке
	 */
	public function get($params)
	{
		$condition = "WHERE 1";

        if (!empty($params['lang_code'])) {
            $condition .= Database::qoute(' AND lang_code = ?s', $params['lang_code']);
        }

        if (!empty($params['lang_id'])) {
            $condition .= Database::qoute(' AND lang_id = ?s', $params['lang_id']);
        }

        if (!empty($params['name'])) {
            $condition .= Database::qoute(' AND name = ?s', $params['name']);
        }

        if (!empty($params['status'])) {
            $condition .= Database::qoute(' AND status = ?s', $params['status']);
        }

        if (!empty($params['country_code'])) {
            $condition .= Database::qoute(' AND country_code = ?s', $params['country_code']);
        }

        $lang_data = Database::get_array("SELECT * FROM snote_languages ?a", $condition);

        return $lang_data;
	}

	/**
	 * Получение списка установленных языков
	 * @return array Список кодов установленных языков
	 */
	public function get_available_languages()
	{
		$languages = Database::get_fields("SELECT lang_code FROM snote_languages");

		return $languages;
	}

	/**
	 * Получение списка языковых переменных и их значений
	 * @param  string $lang_code  Код языка
	 * @return array         	  Список языковых переменных и их значений
	 */
	public function get_language_variables($lang_code = '')
	{
		if (empty($lang_code)) {
			$lang_code = $this->_language;
		}

		$condition = Database::quote("WHERE 1 AND lang_code = ?s", $lang_code);

		$lang_variables_data = Database::get_single_hash_array("SELECT name, value FROM snote_language_variables $condition", array('name', 'value'));

		return $lang_variables_data;
	}
}