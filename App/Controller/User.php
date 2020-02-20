<?php


namespace App\Controller;


use App\Model\Product as ProductModel;
use App\Repository\UserRepository;
use App\Service\FolderService;
use App\Service\ProductService;
use App\Service\RequestService;
use App\Service\UserService;
use App\Service\VendorService;

class User extends AbstractController
{


    /**
     * @param UserRepository $userRepository
     * @param UserService $userService
     *
     * @Route(url="/user/login")
     *
     * @return \App\Http\Response
     */
    public function login(UserRepository $userRepository, UserService $userService) {
        $login = $this->request->getStringFromPost('login');
        $password = $this->request->getStringFromPost('password');


        /**
         * @var $user \App\Model\User
         */
        $user = $userRepository->findByName($login);

        $error_msg = 'User not found or data is incorrect';

        if (is_null($user)) {
            echo $error_msg;
            exit;
        }

        $password = $userService->generatePasswordHash($password);

        if ($user->getPassword() !== $password) {
            echo $error_msg;
            exit;
        }

        $_SESSION['user_id'] = $user->getId();

        return $this->redirect('/');
    }

    /**
     * @Route(url="/user/logout")
     */
    public function logout() {
        unset($_SESSION['user_id']);

        return $this->redirect('/');
    }

    public static function edit() {
        $user = user();

        smarty()->assign_by_ref('user', $user);
        smarty()->display('user/edit.tpl');
    }

    public static function editing() {
        $user = user();

        $name = RequestService::getStringFromPost('name');
        $email = RequestService::getStringFromPost('email');
        $password = RequestService::getStringFromPost('password');
        $password_repeat = RequestService::getStringFromPost('password_repeat');

        if ($password !== $password_repeat) {
            die('Passwords mismatch');
        }

        $is_email_exist = UserService::isEmailExist($email);

        if ($is_email_exist) {
            die('email is busy');
        }

        $password = UserService::generatePasswordHash($password);

        $user->setEmail($email);
        $user->setName($name);
        $user->setPassword($password);

        if (!$user->getId()) {
            mail($email, 'Ура, вы успешно зарегистрировались', 'Вы зарегистрировались как: ' . $name);
        }

        UserService::save($user);
        RequestService::redirect('/');
//        smarty()->assign_by_ref('user', $user);
//        smarty()->display('user/edit.tpl');
    }
}