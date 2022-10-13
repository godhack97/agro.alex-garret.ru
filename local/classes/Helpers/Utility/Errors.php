<?
namespace Godra\Api\Helpers\Utility;

class Errors
{
    /**
     * Сообщения об ошибках [ errors => [1,2,3..] ]
     *
     * @var array
     */
    protected static $message = [];

    /**
     * Ошибка вызова метода
     *
     * @return array
     */
    public static function notMethod($method)
    {
        self::$message['errors'][] = 'Запрашиваемого метода ('.$method.') не существует';
        return self::$message;
    }
}
?>