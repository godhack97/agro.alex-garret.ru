## МЕНЮ:

<div>
  <details>
      <summary>
          <b>Роутинг | Routing</b>
      </summary>

  * [Описание принципов работы](#Описание-принципов-работы)
  * [Методы для фронт разработчика](#Методы-для-фронт-разработчика)
  * [Добавление нового метода](#Добавление-нового-метода)
  * [Обработка ошибок](#Обработка-ошибок)

  </details>

  <details>
      <summary>
          <b>Конструктор</b>
      </summary>

  * [Описание системы конструктора](#Описание-системы-конструктора)
  * [Переменные](#Переменные)
  * [Обязательные методы абстрактных классов](#Обязательные-методы-абстрактных-классов)

  </details>

  <details>
      <summary>
          <b>Классы</b>
      </summary>

  * [Basket](#Basket)
  * [Catalog](#Catalog)
  * [Core](#Core)
  * [EventHandlers](#EventHandlers)
  * [Helpers](#Helpers)
  * [HighloadBlock](#HighloadBlock)
  * [Iblock](#Iblock)
  * [Information](#Information)
  * [Integration](#Integration)
  * [Routing](#Routing)
  * [Services](#Services)
  * [User](#User)

  </details>

</div>

-------
---

## Роутинг | Routing

### Описание принципов работы
Роутинг устроен таким образом, что все запросы приходящие на ```/api``` Переадресовываются на массив ```methods```.

В Core/Init.php Обрабатывается запрос и вызывается роутинг, в роутинге проверяется наличие текущего метода в массиве `$methods`.

Если адрес найден в виде ключа массива ```$methosd``` , создаётся экземпляр класса и вызывается указанный метод этого класса.

В данном примере при `POST` запросе на адрес `/api/users/add` произойдёт следующее:
```php
$methods = [
    '/api/users/add' => [Api\User\Add::class, 'addUser']
]
if($methods[$url])
    (new $methods[$url][0])->$methods[$url][1]
```
Далее Routing отдаст результат выполнения в `Core\Init` , а оттуда данные уйдут в `response`

### Методы для фронт разработчика
+ #### Методы зпроса полей
>>> #### /api/map

+ #### Пользователи и авторизация
+ 
+ #### /api/logout
+ #### /api/auth
+ #### /api/isauth
+ #### /api/register
+ #### /api/restore_password
+ #### /api/change_password
+ #### /api/users/add
+ #### /api/users/update
+ #### /api/users/delete
+ #### /api/users/get

+ #### /api/breadcrumbs
+ #### /api/menu
+ #### /api/callback_form
+ #### /api/get_contacts
+ #### /api/banners/get
+ #### /api/stock/get
+ #### /api/catalog/get
+ #### /api/catalog/buble
+ #### /api/basket/add
+ #### /api/basket/delete
+ #### /api/basket/get
+ #### /api/basket/remove
+ #### /api/basket/count
+ #### /api/dashboards/get

### Добавление нового метода

### Обработка ошибок

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
















Поля запроса можно получить так:
```Js
    fetch('/api/map', {
        method: 'post',
        credentials: 'include',
        body: JSON.stringify({
            url: '/api/auth'
        })
    })
```

вернет массив
```php
    [
        'map' => ['name', 'name' ..], //Принимаемые параметры
        'url' => '/api/.../',  //урл зпроса
    ]
```

-----------------------------------------------------------------------
<h3> Текущие методы </h3>

```PHP
    [
        'Логаут'                =>  '/api/logout',
        'проверка авторизации'  =>  '/api/isauth',
        'получить баннера'      =>  '/api/banners/get',
        'регистрация по форме'  =>  '/api/register',
        'получить контакты'     =>  '/api/get_contacts',
        'Получить поля метода'  =>  '/api/map',
        'получить дешборды'     =>  '/api/dashboards/get',
        'авторизация по логин/пароль'  =>  '/api/auth',
        'Форма обратной связи'  =>  '/api/callback_form',
        'поменять пароль |шаг 2| используя код из шага 1'  =>  '/api/change_password',
        'Восстановить пароль |шаг 1| для получения проверочного кода'  =>  '/api/restore_password'
    ]
```
--------------------------------------------------------------------------------------------------------
Везде где требуется авторизация , параметр

```Javascript
{
    credentials: 'include'
}
```

 обязателен;