<?
namespace Godra\Api\Routing;

use \Godra\Api\Helpers\Auth;

class Route
{
    /**
     * массив методов [сатитк класс , метод]
     *
     * @var array
     */
    protected static $methods = [
        '/api/auth' => [Auth\Authorisation::class, 'authByPassword'],
        '/api/register' => [Auth\Registration::class, 'registerByPassword'],
        '/api/restorepassword' => [Auth\Restore::class, 'restorePassword'],
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
            return 'Error, methods '.$link.' is undefined';
    }

    protected static function formatEventName($method_path_array)
	{
		$strName = '';

        if(isset($method_path_array) AND is_array($method_path_array))
        {
            $strName .= (is_object($method_path_array[0]) ? get_class($method_path_array[0]) : $method_path_array[0]).'::'.$method_path_array[1];
        }

		return $strName;
	}
}