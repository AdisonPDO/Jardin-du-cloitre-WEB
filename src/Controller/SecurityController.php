<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class SecurityController extends AbstractController
{
    /**
     * @Route ("/inscription", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        // todo encode password 
        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
   }

   /**
    * @Route("/login", name="app_login")
    */
   public function login(Request $request , AuthenticationUtils $authenticationUtils): Response
   {
      
        if ($this->getUser()) {
            return $this->redirectToRoute('menu');
        }

       // get the login error if there is one
       $error = $authenticationUtils->getLastAuthenticationError() ? $authenticationUtils->getLastAuthenticationError() : "";
       // last username entered by the user
       $lastUsername = $authenticationUtils->getLastUsername();

       return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'request'=> $request]);
   }

   
   /**
    * @Route("/logout", name="app_logout")
    */
   public function logout()
   {
       throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
   }

   /**
    * @Route("/compte", name="app_compte")
    */
    public function compte(Request $request, UserInterface $user, ObjectManager $manager,  UserPasswordEncoderInterface $encoder)
    {
        $user = new User;
        $user = $this->getUser();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if( !empty($request->request->get('newPhone'))){
            $newPhone = $request->request->get('newPhone');
            $user->setPhone($newPhone);
        }
        if( !empty($request->request->get('newMail'))){
            $newMail = $request->request->get('newMail');
            $user->setMail($newMail);
        }
        if( !empty($request->request->get('password'))){
            $password = $request->request->get('password');
            $match = $encoder->isPasswordValid($user, $password);
            // dump($match);

            if($match = true){
                $nP = $request->request->get('newPassword');
                $nC = $request->request->get('newConfirm');
                
                if($nP === $nC){
                     $hashConfirm = $encoder->encodePassword($user, $nC);
                     $user->setPassword($hashConfirm);
                     dump($hashConfirm);
                     $manager->flush();
                     $this->addFlash('success', 'Vos modiffications ont été prise en compte');
                }
            }
        }
        if(!empty($request->request->get('confirmDelete'))){

            $confirmDelete = $request->get('confirmDelete');
            $match = $encoder->isPasswordValid($user, $confirmDelete);
            if($match = true){
                $this->get('security.token_storage')->setToken(null);
                $manager->remove($user);
                $manager->flush();
                return $this->redirect('/logout');
            }
        }
        $manager->flush();
        $this->getDoctrine()->getManager()->refresh($user);
        return $this->render('security/compte.html.twig');
    }
}
