<?
namespace Godra\Api\Basket;

class AddProduct extends Base
{
    /**
     * Отдаётся при /api/map
     * @var array
     */
    protected static $row_data = [
        'element_id' => [
            'mandatory' => true,
            'alias' => 'PRODUCT_ID',
            'description' => 'Ид товара'
        ],
        'quantity' => [
            'mandatory' => false,
            'alias' => 'QUANTITY',
            'default' => 1,
            'description' => 'Кол-во товара , по умолчанию 1'
        ],
    ];

    /**
     * Апи код информационного блока каталога
     * @var string
     */
    protected static $api_ib_code = IBLOCK_CATALOG_API;

    /**
     * Добавить товар в корзину по id
     */
    public function byId()
    {
        $this->addProductById(
            $this->post_data['element_id'],
            $this->post_data['quantity'] ?: $this->row_data['quantity']['default']
        );
    }
}
?>