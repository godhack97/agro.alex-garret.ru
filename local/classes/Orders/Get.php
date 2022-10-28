<?
namespace Godra\Api\Orders;

class Get extends Base
{
    /**
     * Отдаётся при /api/map
     * @var array
     */
    protected static $row_data = [];


    /**
     * Добавить товар в корзину по id
     */
    public function getAll()
    {
        return $this->getOrders();
    }
}
?>