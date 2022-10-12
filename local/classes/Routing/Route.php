<?
namespace Godra\Api\Routing;

use \Godra\Api\Helpers\Auth,
    \Godra\Api;



class Route
{
    /**
     * массив методов [сатитк класс , метод]
     *
     * @var array
     */
    protected static $methods = [
        // авторизация по логин/пароль
        '/api/auth' => [Auth\Authorisation::class, 'authByPassword'],

        // проверка авторизации
        '/api/isauth' => [Auth\Authorisation::class, 'isAuth'],

        // регистрация по форме
        '/api/register' => [Auth\Registration::class, 'registerByForm'],

        // Логаут
        '/api/logout' => [Auth\Logout::class, 'logoutSelfUser'],

        // Восстановить пароль |шаг 1| для получения проверочного кода
        '/api/restore_password' => [Auth\Restore::class, 'forEmailOrPhone'],

        // поменять пароль |шаг 2| используя код из шага 1
        '/api/change_password' => [Auth\Restore::class, 'changePassword'],

        // получить контакты
        '/api/get_contacts' => [Api\Information\Contacts::class, 'getList'],
    ];

    /**
     * Роутинг для методов
     *
     * @param string $link
     * @return void
     */
    public static function toMethod($link)
    {
        if(self::$methods[$link])
            return self::formatEventName(self::$methods[$link]);
        else
            return ['errors' => 'Error, methods '.$link.' is undefined'];
    }

    /**
     * Метод хелпер для вызова метода из элементов массива [name::class, method]
     *
     * @param array $method_path_array [name::class, method]
     * @return string
     */
    protected static function formatEventName(array $method_path_array)
	{
		$strName = '';

        if(isset($method_path_array) AND is_array($method_path_array))
        {
            $strName .= (is_object($method_path_array[0]) ? get_class($method_path_array[0]) : $method_path_array[0]).'::'.$method_path_array[1];
        }

		return $strName;
	}
}