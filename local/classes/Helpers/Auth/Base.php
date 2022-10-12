<?
namespace Godra\Api\Helpers\Auth;

use \Bitrix\Main\UserTable,
    \Godra\Api\Helpers\Utility\Misc,
    \Bitrix\Main\UserPhoneAuthTable;

class Base
{

    public static function getPhoneByEmail($email)
    {
        $user = UserTable::getList([
            'select' => ['ID'],
            'filter' => ['EMAIL' => $email]
        ])->fetch()['ID'];

        if($user['ID'])
        {
            // Получить телефон, регистрационный или если нету - обычный
            $phone = UserPhoneAuthTable::getList([
                'filter' => ['USER_ID' => $user['ID']]
            ])->fetch()['PHONE_NUMBER'] ?: $user['PERSONAL_PHONE'];
        }

        return $phone;
    }

    /**
     * Получить логин по телефону или email
     * @param string $row_name Поле которое мы хотим получить
     * @param string $email_or_phone email или телефон пользователя
     * @return string
     */
    public static function getDataByLogin($email_or_phone, $row_name = 'LOGIN')
    {
        Misc::includeModules(['main']);

        $type = \strpos($email_or_phone, '@') ? 'EMAIL' : 'PHONE_NUMBER';

        if($type == $row_name)
            return $email_or_phone;

        if($type == 'PHONE_NUMBER')
        {
            $user_id = \Bitrix\Main\UserPhoneAuthTable::getList([
                'filter' => ['PHONE_NUMBER' => $email_or_phone]
            ])->fetch()['USER_ID'];
        }
        else
        {
            $user_id = UserTable::getList([
                'select' => ['ID'],
                'filter' => ['EMAIL' => $email_or_phone]
            ])->fetch()['ID'];
        }


        $res = \CUser::GetList(
                'sort',
                'asc',
                ['ID' => $user_id]
            )->Fetch()[$row_name] ?:

            UserTable::getList([
                'select' => [$row_name],
                'filter' => ['ID' => $user_id]
            ])->fetch()[$row_name];

        if(empty($res))
                $data['errors'] = 'Поле '.$row_name.' не найдено';

        return $res;
    }

    public static function checkConfirmCode($login, $code)
    {
        return ($code == self::getDataByLogin($login, 'UF_CONFIRM_CODE'));
    }

    /**
     * Сменить пароль
     *
     * @param string $login телеон или почта
     * @return void
     */
    public static function changePassword()
    {
        global $USER;

        if (!is_object($USER)) $USER = new \CUser;

        $data = Misc::getPostDataFromJson();

        if(self::checkConfirmCode($data['login'], $data['code']) /*AND $_SESSION['CONFIRM_CODE']*/)
        {
            $id = self::getDataByLogin($data['login'], 'ID');
            $user = new \CUser;
            $result['success'] = $user->Update($id, [
                'UF_CONFIRM_CODE' => rand(0000,9999),
                'PASSWORD' => $data['password'],
                'CONFIRM_PASSWORD' => $data['password']
            ]);

            if($result['success'])
                $user->Update($id, [
                    'UF_CONFIRM_CODE' => rand(0000,9999),
                ]);

            if($result["TYPE"] != "OK")
                $result['errors'][] = 'Не получилось сменить пароль, обратитесь в поддержку';
        }
        else
            $result['errors'][] = 'Введен неверный проверочный код';

        return $result;
    }
}
?>