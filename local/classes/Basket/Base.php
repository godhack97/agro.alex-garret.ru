<?
namespace Godra\Api\Basket;

use \Bitrix\Sale,
    \Bitrix\Sale\Order,
    \Bitrix\Main\Context,
    \Godra\Api\Catalog\Element,
    \Bitrix\Catalog\MeasureTable,
    \Bitrix\Main\Engine\CurrentUser,
    \Godra\Api\Helpers\Utility\Misc,
    \Bitrix\Currency\CurrencyManager,
    \Bitrix\Sale\Delivery\Services\Manager,
    \Bitrix\Sale\Delivery\Services\EmptyDeliveryService;
use Godra\Api\Helpers\Utility\Errors;

abstract class Base
{
    protected $user;
    protected $fuser;
    protected $basket;
    protected $site_id;
    protected $post_data;

    function __construct()
    {
        Misc::checkRowsV2($this->post_data, $this->row_data);
        Misc::includeModules(['iblock', 'catalog', 'sale', 'currency']);

        $this->site_id   = Context::getCurrent()->getSite();
        $this->user      = $this->getUser();
        $this->fuser     = $this->getFuserId();
        $this->currency  = $this->getBaseCurrency();
        $this->basket    = $this->getBasketByFuser();
        $this->post_data = Misc::getPostDataFromJson();
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
     * Получить единицу измерения по коду
     *
     * @param string $code Код единицы измерения
     * @return array|
     */
    protected function getMeasureByCode($code)
    {
        return MeasureTable::getList([
            'filter' => [ 'SYMBOL' => $code ],
            'limit'  => 1,
        ])->fetch();
    }

    /**
     * Проверить наличие товара в корзине
     *
     * @param int $id Ид товара
     * @param string $measure_code название единицы измерения
     * @return boolean
     */
    protected function GetExistsBasketItem($id, $measure_code = false)
    {
        $result = false;

        if(empty($id) OR intval($id) <= 0 OR intval($id) != $id)
            return $result;

        foreach ($this->basket as $item)
        {
            if((int) $id == $item->getProductId())
            {
                if($measure_code)
                {
                    if(($item->getField('MEASURE_CODE') == $measure_code))
                    {
                        $result = $item;
                        break;
                    }
                }
                else
                {
                    $result = $item;
                    break;
                }
            }
        }

        return $result;
     }

    /**
     * Добавить товар в текущую корзину по id товара
     * @param int $id
     * @param array $measure Единица измерения , {name: 'название из админки', value: 'коофициент относительно базовой единицы' }
     * @param int $quantity
     */
    protected function addProductById($id, $measure_code = false)
    {

        if($measure_code)
        {
                $measure = $this->getMeasureByCode($measure_code);
                $cooficient = Misc::getMeasureCooficientByProductId($id);
        }

        $quantity = $this->post_data['quantity'] ?: 1;

        if ($item = $this->GetExistsBasketItem( $id, $measure['CODE'] ? : false ))
        {
            $item->setField('QUANTITY', $item->getQuantity() + (int) $quantity);
        }
        else
        {
            $item = $this->basket->createItem('catalog', $id);

            $item->setFields([
                'QUANTITY' => (int) $quantity,
                'LID'      => 's1',
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            ]);

            // Если передана единица измерения
            if($measure_code AND $measure)
            {
                $item->setField('MEASURE_CODE', (int) $measure['CODE']);
                $item->setField('MEASURE_NAME', $measure['MEASURE_TITLE']);
                $item->setField('CUSTOM_PRICE', 'Y');
                $item->setField('PRICE', $item->getPrice()*$cooficient);
            }

        }

        return $this->basket->save();
    }

    /**
     * Уменьшить кол-во товара в текущуей корзину по id товара
     * @param int $id
     * @param int $quantity
     */
    protected function removeProductById($id, $measure_code = false)
    {
        if($measure_code)
        {
            $measure = $this->getMeasureByCode($measure_code);
        }

        $quantity = $this->post_data['quantity'] ?: 1;

        if ($item = $this->GetExistsBasketItem( $id, $measure['CODE'] ? : false ))
            $item->getQuantity() > $quantity ?
                $item->setField('QUANTITY', $item->getQuantity() - $quantity):
                $item->delete();

        $this->basket->save();
    }

    /**
     * Удалить товар из корзины по id товара
     * @param int $id
     */
    protected function deleteProductById($id, $measure_code = false)
    {
        if($item = $this->GetExistsBasketItem( $id, $measure['CODE'] ? : false ))
            $item->delete();

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
            $this->site_id
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
                'id'           => $item->getId(),
                'name'         => $item->getField('NAME'),
                'price'        => $item->getPrice(),
                'props'        => $item->getPropertyCollection()->getPropertyValues(),
                'weight'       => $item->getWeight(),
                'can_buy'      => $item->canBuy(),
                'quantity'     => $item->getQuantity(),
                'product_id'   => $item->getProductId(),
                'final_price'  => $item->getFinalPrice(),
                'measure_code' => $item->getField('MEASURE_CODE'),
                'measure_name' => $item->getField('MEASURE_NAME'),

            ];

        return $result;
    }

    /**
     * Получить базовую валюту
     */
    public function getBaseCurrency()
    {
        return CurrencyManager::getBaseCurrency();
    }

    /**
     * Создать заказ
     */
    public function addOrder()
    {
        if(count($this->basket)<=0)
           Errors::add("Ваша корзина пуста");

        $delyvery_id = $this->post_data['delivery_id'] ?:
            EmptyDeliveryService::getEmptyDeliveryServiceId();

        if($order = Order::create(SITE_ID, $this->user['id'], $this->currency))
        {
            $order->setPersonTypeId(1);
            $order->setBasket($this->basket);

            /* Доставка */
            $shipmentCollection = $order->getShipmentCollection();
            $shipment = $shipmentCollection->createItem();
            $service  = Manager::getById($delyvery_id);

            $shipment->setFields([
                'DELIVERY_ID' => $service['ID'],
                'DELIVERY_NAME' => $service['NAME'],
            ]);

            $shipment_item_collection = $shipment->getShipmentItemCollection();

            foreach ($this->basket as $item)
            {
                $shipment_item = $shipment_item_collection->createItem($item);
                $shipment_item->setQuantity($item->getQuantity());
            }
            /* /Конец доставки */

            /* Свойства заказа */
            $select_props = ['FIO', 'PHONE', 'EMAIL'];
            $property_collection = $order->getPropertyCollection();
            $property_code_to_id   = [];

            foreach($property_collection as $prop_value)
                $property_code_to_id[$prop_value->getField('CODE')] = $prop_value->getField('ORDER_PROPS_ID');

            foreach ($select_props as $prop_code)
                if($this->post_data[$prop_code])
                {
                    $prop_value = $property_collection->getItemByOrderPropertyId(\strtoupper($prop_code));
                    $prop_value->setValue($this->post_data[$prop_code]);
                }

            /* /Конец свойств заказа */
            $order->doFinalAction(true);

            $result = $order->save();

            if(!$result->isSuccess())
                Errors::add("Ошибка создания заказа: ".implode(", ",$result->getErrorMessages()));

            return $order->getField('ACCOUNT_NUMBER');
        }

    }
}
?>