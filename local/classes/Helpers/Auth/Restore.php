<?
namespace Godra\Api\Helpers\Auth;

use \Godra\Api\Integration\SMSSeveren\Send;

class Restore extends Base
{
    protected $data_rows = [
        'login',
        'password',
        'code'
    ];

    public function setConfirmCodeByLogin($email_or_phone)
    {
        $type = \strpos($email_or_phone, '@') ?
            'EMAIL':
            'PHONE_NUMBER';

        $id    = $this->getDataByLogin($email_or_phone, 'ID');
        $phone = $type == 'EMAIL' ? $this->getDataByLogin($email_or_phone, 'PHONE_NUMBER') : $email_or_phone;

        $_SESSION['CONFIRM_CODE'] = rand(0000, 9999);

        $user = new \CUser;
        $user->Update($id, ['UF_CONFIRM_CODE' => $_SESSION['CONFIRM_CODE']]);

        // формируем сообщение и отправляем
        $sms  = (new Send([
                    'phone' => $phone,
                    'text' => 'Ваш проверочный код : '.$_SESSION['CONFIRM_CODE']
                ]))->send();

        return $sms;
    }

    public function forEmailOrPhone()
    {
        $this->setConfirmCodeByLogin($this->data['login']);
    }
}
?>