<?
namespace Godra\Api\User;

class Get extends Base
{
    protected $select_rows = [
        [ 'name' => 'NAME' ], // имя
        [ 'name' => 'LAST_NAME'], // фамилия
        [ 'name' => 'SECOND_NAME'], // отчество
        [ 'name' => 'LOGIN'], // логин
        [ 'name' => 'EMAIL'], // Email
        [ 'name' => 'ID'], // id
        // [ 'name' => 'UF_POINT_OF_SALE'] // торговые точки
    ];

    public function GetUsers()
    {
        return $this->get();
    }
}
