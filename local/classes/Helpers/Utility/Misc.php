<?
namespace Godra\Api\Helpers\Utility;

use Bitrix\Main\Application,
    Bitrix\Main\HttpRequest,
    Bitrix\Main\Web\HttpClient;

class Misc {

    /**
     * Проверяет текущий путь, api запрос это или нет
     *
     * @param string $requestPage путь к текущей странице
     * @return void
     */
    public static function checkRequestPage($requestPage)
    {
        return (strpos($requestPage, 'api') != false);
    }


    /**
     * Прописать заголовки
     *
     * @param string $type  тип заголовка json/200/mandatory..
     * @return void
     */
    public static function setHeaders($type)
    {
        if($type == 'json')
            header('content-type: application/json');

        if($type == '200')
        {
            header('HTTP/2 200 OK');
            header('Status: 200 OK');
        }

        if($type == 'mandatory')
        {
            header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Credentials: true');
            header('Content-Type: application/json');
            header("Access-Control-Allow-Methods: *");
            header("Access-Control-Allow-Headers: *");
        }
    }

    /**
     * Получить данные из запроса Json
     *
     * @return array
     */
    public static function getPostDataFromJson()
    {
        return \json_decode(HttpRequest::getInput(), true);
    }

    /**
     * массив в json
     *
     * @param array $php_array
     * @return string
     */
    public static function phpToJson($php_array)
    {
        return json_decode($php_array);
    }


    /**
     * Подключение модулей
     *
     * @param array $modules список модулей в массиве
     * @return void
     */
    public static function includeModules(array $modules): void
    {
        foreach ($modules as $module)
            if(!\Bitrix\Main\Loader::includeModule($module))
                throw new \Exception("Не удалось подключить модуль $module", 1);
    }

    /**
     * Форматирование цен
     *
     * @param mixed $price  цена
     * @return void
     */
    public static function priceFormat($price)
    {
        $price = number_format($price, 2, ',', ' ');
        $price = preg_replace('#[,]00#', '', $price);

		return $price;
    }

    /**
     * Сортировка массива по полю
     *
     * @param array $arr массив с данными
     * @param string $fieldName поле для сортировки
     * @param boolean $desc направление сортировки
     * @return void
     */
	public static function aSortByField(array &$arr, string $fieldName, bool $desc = false): void
    {
		uasort($arr, function ($a, $b) use ($fieldName, $desc) {
			return ($a[$fieldName] <=> $b[$fieldName]) * ($desc ? -1 : 1);
		});
	}

    /**
     * Очистка массива от пустых значений
     *
     * @param array $array
     * @return array
     */
    public static function clearArrayOfEmptyValues(array $array): array
    {
        return array_diff($array, [null, false, "", " "]);
    }

    /**
     * Implode() с удалением пустых значений из массива строк
     *
     * @param array $strings массив строк
     * @param string $glue разделитель, по умолчанию пробел
     * @return string
     */
    public static function getJoinString(array $strings, string $glue = " "): string
    {
	    return implode($glue, self::clearArrayOfEmptyValues($strings));
    }

    /**
     * Генератор паролей
     *
     * @param integer $length длина пароля
     * @return string
     */
    public static function generatePassword(int $length = 10): string
    {
        $alphabet = implode("", range("a", "z"));
        $alphabet .= strtoupper($alphabet);
        $alphabet .= "0123456789!@#\$%&*()-_=";

        $alphabetLength = strlen($alphabet);
        $password = "";

        for ($i = 0; $i < $length; $i++)
            $password .= $alphabet[mt_rand(0, $alphabetLength - 1)];

        return $password;
    }

    /**
     * Подключить файл
     *
     * @param string $relativePath // путь
     * @return void
     */
    public static function inlineFile(string $relativePath): void
    {
	    include Application::getDocumentRoot() . $relativePath;
    }

}