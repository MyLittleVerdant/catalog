<?php
declare(strict_types=1);

namespace console\controllers;

use common\models\User;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

class UserController extends Controller
{
    /**
     * Создает пользователя-администратора
     *
     * @param string $username Имя пользователя
     * @param string $email Email пользователя
     * @param string|null $password Пароль
     * @return int
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionCreate(string $username, string $email, string $password = null): int
    {
        // Проверяем, есть ли уже пользователи
        if (
            User::find()->count() > 0 &&
            !$this->confirm('В базе уже есть пользователи. Продолжить создание нового пользователя?')
        ) {
            return ExitCode::OK;
        }

        // Генерируем пароль, если не указан
        if ($password === null) {
            $password = Yii::$app->security->generateRandomString(12);
            $this->stdout("Сгенерирован пароль: $password\n", BaseConsole::FG_YELLOW);
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($password);
        $user->generateAuthKey();

        if ($user->save()) {
            $this->stdout("Пользователь успешно создан!\n", BaseConsole::FG_GREEN);
            $this->stdout("Имя пользователя: $username\n");
            $this->stdout("Email: $email\n");
            if ($password !== null) {
                $this->stdout("Пароль: $password\n");
            }
            return ExitCode::OK;
        }

        $this->stdout("Ошибка при создании пользователя:\n", BaseConsole::FG_RED);
        foreach ($user->errors as $errors) {
            foreach ($errors as $error) {
                $this->stdout("- $error\n", BaseConsole::FG_RED);
            }
        }
        return ExitCode::DATAERR;
    }
}

