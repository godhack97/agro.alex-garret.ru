<?
namespace Godra\Api\Catalog;

use Godra\Api\Helpers\Utility\Misc;

abstract class Base extends \Godra\Api\Iblock\Base
{
    /**
     * Поля товара для выборки
     * name: имя поля, если нужно алиас => имя
     * method: метод для обработки поля псле получения
     * @var array
     */
    protected static $select_product_rows = [];
    protected static $row_data = [];
    protected static $select_rows = [];
    protected static $api_ib_code = false;

    function __construct()
    {
        Misc::includeModules(['iblock', 'catalog', 'sale']);
    }
}
?>