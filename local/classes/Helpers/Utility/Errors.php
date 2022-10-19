<?
namespace Godra\Api\Helpers\Utility;

class Errors
{
    /**
     * Сообщения об ошибках [ errors => [1,2,3..] ]
     */
    protected static $message = [];

    /**
     * Ошибка вызова метода
     */
    public static function notMethod($method)
    {
        self::$message['errors'][] = 'Запрашиваемого метода ('.$method.') не существует';
        return self::$message;
    }

    /**
     * Ошибка авторизации
     */
    public static function dontAuth()
    {
        global $API_ERRORS;
        $API_ERRORS[] = 'Авторизуйтесь';
    }

    /**
     * Ошибка авторизации
     */
    public static function dontHaveRow($name)
    {
        global $API_ERRORS;
        $API_ERRORS[] = 'Не заполнено обязательное поле '.$name;
    }
}
?>