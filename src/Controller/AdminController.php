<?php
namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//use EasyCorp\Bundle\EasyAdminBundle\Tests\Fixtures\AppTestBundle\Controller\AdminController;

class AdminController extends EasyAdminController{

      /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserController constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function persistUserEntity($user)
    {
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $user->getPassword())
        );

        // $this->encodePassword($entity);
        parent::persistEntity($user);
    }

    public function updateUserEntity($user)
    {
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $user->getPassword())
        );
        parent::updateEntity($user);
    }
}
