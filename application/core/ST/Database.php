<?php

/*
 * (c) Arefiev Artem, Sidorov Andrew
 * License for snote project
 */

namespace ST;

class Database
{
    /*
     * Используется для передачи методам класса индентификатора коннекта к БД
     */
    private static $_db;

    private function __construct() { }

    /*
     * Подключение к базе данных
     * @param string $db_host     host name
     * @param string $db_user     user name
     * @param string $db_password user password
     * @param string $db_name     database
     * return resource идентификатор коннетка к БД, false если возникает ошибка
     */
    public static function connect( $config )
    {
        $db_host = $config['system']['db_host'];
        $db_user = $config['system']['db_user'];
        $db_password = $config['system']['db_password'];
        $db_name = $config['system']['db_name'];

        if ( $link = mysqli_connect( $db_host, $db_user, $db_password, $db_name ) ) {
            self::$_db = $link;

            return true;
        }
        return false;
    }

    /*
     * Получает данные из БД в виде массива. В качестве ключей - поля БД.
     * @param string $query SQL query
     * return одномерный, многомерный массив, если полученных строк больше одной
     */
    public static function get_array( $query )
    {
        $args = func_get_args();
        $result = array();

        if ( $_result = mysqli_query(self::$_db, self::query($query, array_slice($args, 1))) ) {
            if ( $_result->num_rows > 1 ) {
                while ( $_res = mysqli_fetch_assoc($_result) ) {
                    $result[] = $_res;
                }
            }
            else $result = mysqli_fetch_assoc($_result);

            mysqli_free_result($_result);
        }

        return !empty($result) ? $result : array();
    }

    /**
     * Execute query and format result as associative array with column names as keys and index as defined field
     *
     * @param string $query unparsed query
     * @param string $field field for array index
     * @param mixed ... unlimited number of variables for placeholders
     * @return array structured data
     */
    public static function get_hash_array($query, $field)
    {
        $args = array_slice(func_get_args(), 2);
        array_unshift($args, $query);

        if ( $_result = mysqli_query(self::$_db, self::query($query, array_slice($args, 0))) ) {
            while ($arr = mysqli_fetch_assoc($_result)) {
                if (isset($arr[$field])) {
                    $result[$arr[$field]] = $arr;
                }
            }

            mysqli_free_result($_result);
        }

        return !empty($result) ? $result : array();
    }

    /*
     * Получает данные из БД в виде одномерного массива. В качестве ключей 0..N.
     */
    public static function get_fields( $query )
    {
        $args = func_get_args();
        $result = array();

        if ( $_result = mysqli_query(self::$_db, self::query($query, array_slice($args, 1))) ) {
            while ( $_res = mysqli_fetch_row($_result) ) {
                $result[] = $_res[0];
            }

            mysqli_free_result($_result);
        }

        return !empty($result) ? $result : array();
    }

    /*
     * Получает значение первого элемента из БД
     */
    public static function get_field( $query )
    {
        $args = func_get_args();

        if ( $_result = mysqli_query(self::$_db, self::query($query, array_slice($args, 1))) ) {
            $result = mysqli_fetch_row($_result);

            mysqli_free_result($_result);
        }

        return ( isset($result) && is_array($result) ) ? $result[0] : "";
    }

    /*
     * Получает количество найденных строк
     */
    public static function get_rows_count( $query )
    {
        if ( $_result = mysqli_query(self::$_db, $query) ) {
            $result = $_result->num_rows;

            mysqli_free_result($_result);
        }

        return isset($result) ? $result : "";
    }

    /*
     * Получает количество найденных полей
     */
    public static function get_fields_count( $query )
    {
        if ( $_result = mysqli_query(self::$_db, $query) ) {
            $result = $_result->field_count;

            mysqli_free_result($_result);
        }

        return isset($result) ? $result : "";
    }

    public static function query( $query, $args )
    {
        $query = self::process($query, $args);
        return $query;
    }

    private static function process( $pattern, $data = array() )
    {
        if ( ! empty($data) && preg_match_all("/\?(i|s|l|d|a|n|u|e|m|p|w|f)+/", $pattern, $m) ) {
            $length = 2;

            foreach ($m[0] as $key => $ph) {
                if ( $ph == '?i' ) { // integer
                    $pattern = substr_replace($pattern, (int)$data[$key], strpos($pattern, $ph), $length);
                } elseif ( $ph == '?f' ) { // float
                    $pattern = substr_replace($pattern, sprintf('@01.2f', $data[$key]), strpos($pattern, $ph), $length);
                } elseif ( $ph == '?s' ) { // string
                    $pattern = substr_replace($pattern, "'" . $data . "'", strpos($pattern, $ph), $length);
                } elseif ( $ph == '?n' ) { // integer array
                    // FIXME
                    foreach ($data as $key => $v) {
                        $data[$key] = (int)$v;
                    }
                    $pattern = substr_replace($pattern, self::implode_trim(', ', $data), strpos($pattern, $ph), $length);
                } elseif ( $ph == '?d' ) { // float array
                    // FIXME
                    foreach ($data as $key => $v) {
                        $data[$key] = sprintf('@01.2f', $v);
                    }
                    $pattern = substr_replace($pattern, self::implode_trim(', ', $data), strpos($pattern, $ph), $length);
                } elseif ( $ph == '?a' ) { // string array
                    $pattern = substr_replace($pattern, self::implode_trim(', ', $data, true), strpos($pattern, $ph), $length);
                }
            }
        }

        return $pattern;
    }

    /*
     * Преобразование массива в строку, удалением пробелов и добавлением обертывающих $quotes
     * param@ $glue       string  строка - разделитель
     * param@ $use_quotes boolean false - не использовать обертку
     * param@ $array      array   исходный массив
     * return string результирующая строка вида '$string', ..., '$string_n' или $string, ..., $string_n
     * (!) Работает с int, string
     */
    private static function implode_trim( $glue = ', ', $array = array(), $use_quotes = false )
    {
        if ( $use_quotes ) {
            $quotes = "'";
        } else {
            $quotes = "";
        }

        foreach ($array as $key => $value) {
            $array[$key] = $quotes . trim($value) . $quotes; // удаление лишних пробелов в начале и в конце строки и добавление обертки
        }

        return implode($glue, $array);
    }

  //  public static function query($query)
  //  {
  //    $link = self::$_db;
  //    $result = array();
  //    $_result = mysqli_query($link, $query);
  //
  //    while ($_res = mysqli_fetch_assoc($_result)) {
  //      $result[] = $_res;
  //    }
  //
  //    print_r($result);
  //
  ////    mysqli_free_result($result);
  //  }
  }