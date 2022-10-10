<?
namespace Godra\Api\Services;

use \Godra\Api\Helpers\Utility\Misc;

class Form
{
    /**
     * Данные формы / Данные вопросов / Данные ответов
     *
     * @var array
     */
    protected $form = [];
    protected $result = [];


    /**
     * Класс для работы с формами
     *
     * @param string $sid S_ID (символьный id) формы
     * @param array $data Данные с формы
     */
    function __construct($sid, $data)
    {
        Misc::includeModules(['form']);

        $this->form['result'] = $data;
        $this->form['id'] = \CForm::GetList($by="s_id", $order="desc", ['SID' => $sid])->fetch()['ID'];

        if(!$this->form['id'])
            $this->result['errors'][] = 'Форма не найдена';
    }

    /**
     * Добавить результат формы
     *
     * @return void
     */
    public function addResult()
    {
        $this->getFormData();

        foreach ($this->form['questions'] as $key => $question)
        {
            $question_type = $this->form['answers'][$key][0]['FIELD_TYPE'];
            $question_html_id = 'form_'.$question_type.'_'.$question['ID'];

            $result_data[$question_html_id] = $this->form['result'][$question['SID']];
        }

        $response['errors'] = \array_merge(
            $this->result['errors'],
            \CForm::Check($this->form['id'], $result_data)
        );

        if(empty($response['errors']))
            \CFormResult::Add($this->form['id'], $result_data, 'Y', 0);

        return $response;
    }


    /**
     * Получить данные формы
     *
     * @return void
     */
    public function getFormData()
    {
        \CForm::GetDataByID(
            $this->form['id'],
            $this->form['data'],
            $this->form['questions'],
            $this->form['answers'],
            $dropdown,
            $multiselect
        );
    }
}