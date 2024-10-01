<?php
/**
 * @author Skorobogatko Oleksii <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.1
 */

namespace app\commands;

use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use app\base\console\Controller;
use app\models\User;

/**
 * Manages users.
 *
 * @author skoro
 */
class UserController extends Controller
{
    
    /**
     * User list.
     * @param string $filter filter: all, enabled, disabled, pending.
     */
    public function actionIndex($filter = 'all')
    {
        $filters = ['all', 'enabled', 'disabled', 'pending'];
        if (!in_array($filter, $filters)) {
            throw new ConsoleException(Yii::t('app', 'Filter accepts values: {values}', ['values' => implode(',', $filters)]));
        }
        
        $users = User::find();
        switch ($filter) {
            case 'enabled':
                $users->where(['status' => User::STATUS_ENABLED]);
                break;
            
            case 'disabled':
                $users->where(['status' => User::STATUS_DISABLED]);
                break;
            
            case 'pending':
                $users->where(['status' => User::STATUS_PENDING]);
                break;
        }
        
        $this->userList($users->all());
    }
    
    /**
     * Finds user by email or user name.
     * @param string $pattern search pattern.
     */
    public function actionFind($pattern)
    {
        $users = User::find()
            ->where(['like', 'email', $pattern])
            ->where(['like', 'name', $pattern])
            ->all();
        $this->userList($users);
    }
    
    /**
     * Creates a new user.
     * @param string $name user name
     * @param string $email user email
     * @param string $password uncrypted password, if skipped random password will be generated.
     */
    public function actionCreate($name, $email, $password = '')
    {
        if (empty($password)) {
            $random = Yii::$app->security->generateRandomString(8);
        }
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->status = User::STATUS_ENABLED;
        $user->setPassword(empty($password) ? $random : $password);
        if ($user->save()) {
            $this->p('User "{name}" has been created.', ['name' => $user->name]);
            if (empty($password)) {
                $this->p('Random password "{password}" has been generated.', ['password' => $random]);
            }
        }
        else {
            $this->err('Couldn\'t create user.');
            foreach ($user->getErrors() as $attribute => $error) {
                print reset($error) . PHP_EOL;
            }
        }
    }
    
    /**
     * Delete a user.
     * @param string $email user email
     */
    public function actionDelete($email)
    {
        $user = $this->findUser($email);
        if (!$this->confirm('Are you sure to delete user "' . $user->email . '"')) {
            return;
        }
        if ($user->delete()) {
            $this->p('User deleted.');
        }
        else {
            $this->err('Couldn\'t delete user.');
        }
    }
    
    /**
     * Disable user.
     * @param string $email user email
     */
    public function actionDisable($email)
    {
        $user = $this->findUser($email);
        if ($user->status === User::STATUS_DISABLED) {
            throw new ConsoleException(Yii::t('app', 'User "{email}" already disabled.', compact('email')));
        }
        $user->status = User::STATUS_DISABLED;
        if ($user->save()) {
            $this->p('User "{email}" disabled.', compact('email'));
        }
    }
    
    /**
     * Enable user.
     * @param string $email user email
     */
    public function actionEnable($email)
    {
        $user = $this->findUser($email);
        if ($user->status === User::STATUS_ENABLED) {
            throw new ConsoleException(Yii::t('app', 'User "{email}" already enabled.', compact('email')));
        }
        $user->status = User::STATUS_ENABLED;
        if ($user->save()) {
            $this->p('User "{email}" enabled.', compact('email'));
        }
    }
    
    /**
     * Change user password.
     * @param string $email user email
     * @param string $new_password uncrypted password, if skipped random password will be generated.
     */
    public function actionPassword($email, $new_password = '')
    {
        $user = $this->findUser($email);
        if (empty($new_password)) {
            $random = Yii::$app->security->generateRandomString(8);
        }
        $user->setPassword(empty($new_password) ? $random : $new_password);
        if ($user->save()) {
            if (empty($new_password)) {
                $this->p('Password has been changed to random "{random}"', compact('random'));
            } else {
                $this->p('Password has been changed.');
            }
        }
    }
    
    /**
     * Get User model.
     * @param string $email
     * @return User
     * @throws \yii\console\Exception
     */
    protected function findUser($email)
    {
        if (!($user = User::findByEmail($email))) {
            throw new ConsoleException(Yii::t('app', 'User not found.'));
        }
        return $user;
    }
    
    /**
     * @param User[] $users
     */
    protected function userList(array $users)
    {
        if (empty($users)) {
            return $this->p('No users found.');
        }
    
        $this->stdout(sprintf("%4s %-32s %-24s %-10s %-16s %-8s\n", 'ID', 'Email address', 'User name', 'Roles', 'Created', 'Status'), Console::BOLD);
        $this->stdout(str_repeat('-', 94) . PHP_EOL);
        
        foreach ($users as $user) {
            $roles = $user->getRoles();
            printf("%4d %-32s %-24s %-10s %-16s %-8s\n",
                    $user->id,
                    $user->email,
                    $user->name,
                    count($roles) ? reset($roles)->name : '',
                    date('Y-m-d H:i', $user->created_at),
                    $user->getStatusLabel()
            );
            if (count($roles) > 1) {
                array_shift($roles);
                foreach ($roles as $role) {
                    printf("%4s %-32s %-24s %-10s %-16s %-8s\n",
                        '',
                        '',
                        '',
                        $role->name,
                        '',
                        ''
                    );
                }
            }
        }
    }
    
}
