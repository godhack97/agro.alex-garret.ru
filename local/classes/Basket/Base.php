<?
namespace Godra\Api\Basket;

use \Bitrix\Sale,
    \Bitrix\Main\Context,
    \Bitrix\Main\Engine\CurrentUser,
    \Godra\Api\Helpers\Utility\Misc,
    \Bitrix\Currency\CurrencyManager;

abstract class Base
{
    protected $user;
    protected $basket;
    protected $post_data;
    protected $fuser;

    function __construct()
    {
        $this->user      = $this->getUser();
        $this->fuser     = $this->getFuserId();
        $this->basket    = $this->getBasketByFuser();
        $this->post_data = Misc::getPostDataFromJson();

        Misc::checkRowsV2($this->post_data, $this->row_data);
        Misc::includeModules(['iblock', 'catalog', 'sale', 'currency']);
    }

    /**
     * Получить основную информацию пользователя (id, name)
     */
    protected function getUser()
    {
        return [
            'id' => CurrentUser::get()->getId(),
            'name' => CurrentUser::get()->getFormattedName(),
        ];
    }

    /**
     * Добавить товар в текущую корзину по id товара
     * @param int $id
     * @param int $quantity
     */
    protected function addProductById($id, $quantity)
    {
        if ($item = $this->basket->getExistsItem('catalog', $id))
            $item->setField('QUANTITY', $item->getQuantity() + $quantity);
        else
        {
            $item = $this->basket->createItem('catalog', $id);

            $item->setFields([
                'QUANTITY' => $quantity,
                'LID'      => Context::getCurrent()->getSite(),
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            ]);
        }

        $this->basket->save();
    }

    /**
     * Уменьшить кол-во товара в текущуей корзину по id товара
     * @param int $id
     * @param int $quantity
     */
    protected function removeProductById($id, $quantity)
    {
        if ($item = $this->basket->getExistsItem('catalog', $id))
            $item->setField('QUANTITY', $item->getQuantity() - $quantity);

        $this->basket->save();
    }

    /**
     * Удалить товар из корзины по id товара
     * @param int $id
     */
    protected function deleteProductById($id)
    {
        $this->basket->getItemById($id)->delete();
        $this->basket->save();
    }

    /**
     * Получить id текущего покупателя
     * @return int
     */
    protected function getFuserId()
    {
        return Sale\Fuser::getId();
    }

    /**
     * Получить корзину текущего пользователя
     */
    protected function getBasketByFuser()
    {
        return Sale\Basket::loadItemsForFUser(
            $this->fuser,
            Context::getCurrent()->getSite()
        );
    }

    /**
     * Получить кол-во товаров в корзине
     * @return int
     */
    public function getQuantityList()
    {
        return $this->basket->getQuantityList();
    }

    /**
     * Получить товары корзины
     * @return array
     */
    public function getBasketItems()
    {
        $basketItems = $this->basket->getBasketItems();

        foreach ($basketItems as $item)
            $result[] = [
                'id'          => $item->getId(),
                'name'        => $item->getField('NAME'),
                'price'       => $item->getPrice(),
                'props'       => $item->getPropertyCollection()->getPropertyValues(),
                'weight'      => $item->getWeight(),
                'can_buy'     => $item->canBuy(),
                'quantity'    => $item->getQuantity(),
                'product_id'  => $item->getProductId(),
                'final_price' => $item->getFinalPrice(),
            ];
    }

}
?>