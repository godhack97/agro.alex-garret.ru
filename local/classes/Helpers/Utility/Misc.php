<?
namespace Godra\Api\Helpers\Utility;

use Bitrix\Main\Application;

class Misc {

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