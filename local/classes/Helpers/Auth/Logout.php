<?
namespace Godra\Api\Helpers\Auth;

use    \Bitrix\Main\Context;

class Logout
{
    public static function logoutSelfUser()
    {
        global $USER;
        $USER->Logout();
        Context::getCurrent()->getResponse()->writeHeaders();
    }
}