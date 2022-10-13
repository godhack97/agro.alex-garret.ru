<?
namespace Godra\Api\Helpers\Auth;

use \Godra\Api\Integration\Sms\SmsRu;

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
        $sms  = new SmsRu(SMSRU_AUTH_TOKEN);
        $data = new \stdClass();
        $data->to = $phone;
        $data->text = 'Ваш проверочный код : '.$_SESSION['CONFIRM_CODE'];
        $sms = $sms->send_one($data);

        return $data;
    }

    public function forEmailOrPhone()
    {
        $this->setConfirmCodeByLogin($this->data['login']);
    }
}
?>