<?php

namespace App\Controller;
use App\Entity\Menu;
use App\Entity\User;
use App\Entity\Commande;
use App\Form\RegistrationType;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class ApliController extends AbstractController
{
    /**
     * @Route("/", name="menu")
     */
    public function menu(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $repo =$this->getdoctrine()->getRepository(Menu::class);
        $now = new \DateTime();
        $mymenu = $repo->getByDate($now);
        
        if(is_array($mymenu)){
            $mytitle       = 'Il n\'y a pas de Menu Du Jour Aujourd\'hui ';
            $mydescription = '';
            $photos ='';
        }
        else{        
            $mytitle       = $mymenu->getTitle();
            $mydescription = $mymenu->getDescription();
            $photos        = $mymenu->getphotos();
        }
        return $this->render('menu.html.twig', [
            'controller_name' => 'ApliController',
            'titre' => $mytitle, 
            'description' => $mydescription,
            'request'=> $request,
            'logged' => false,
            'authRequired' => false,
            'forget' => false,
            'encoder' => $encoder,
            'manager' => $manager,
            'photos'  => $photos
        ]);
    }

    /**
    * @Route("/reserv", name="resto_reserv")
    */
    public function reserv(Request $request, ObjectManager $manager) {
        $user = new User;
        $user = $this->getUser();
        $repo =$this->getdoctrine()->getRepository(Menu::class);
        $now = new \DateTime();
        $mymenu = $repo->getByDate($now);
        $menuDuJour  = "";
        $nbPlatDispo = "";
        $mydescrip = '';
        $forceRefresh = false;
        
        if(!empty($mymenu) && $mymenu->getNbPlat() > 0 ){
            $nbPlatDispo = 1;
        }
        if(is_array($mymenu)){
            $mydescrip = ' Reservation impossible';
        }
        if(!is_array($mymenu)){
            $menuDuJour = 2;
        $nbCommande = $request->request->get('title');
         
        for ($i = 0; $i < $nbCommande; $i++) {
            if($request->request->count() > 0) {
                $commande = new Commande();
                $commande->setValidation($request->request->get('title'))
                         ->setUser($user)
                         ->setMenu($mymenu)
                         ->setDate(new \DateTime());
                    $manager->persist($commande);
                    $manager->flush();

                if($mymenu->getNbPlat() > 0 ) {
                    $mymenu->setNbPlat($mymenu->getNbPlat() - 1);
                    $manager->persist($mymenu);
                    $manager->flush();
                }
                $forceRefresh = true;
            }
        } 
        if($mymenu->getNbPlat() < 1 ) {
            $nbPlatDispo = -1;
        }
    }
        return $this->render('apli/reserv.html.twig', ['forceRefresh' => $forceRefresh, 'mydescrip' => $mydescrip , 'menuDuJour' => $menuDuJour, "nbPlatDispo" => $nbPlatDispo]);
    }
    
    public function affichageNbPlat(){
        $now = new \DateTime();
        $repo =$this->getdoctrine()->getRepository(Menu::class);
        $mymenu = $repo->getByDate($now);
        $test= 2 ;
        
        if(!is_array($mymenu)){
            $test = 1;
        }

        if ($test == 1) {
        return $this->render('affichageNbPlat.html.twig', [ 'mymenu' => $mymenu->getNbPlat(), 'test' => $test ]);
        }
        if($test == 2) {
            return $this->render('affichageNbPlat.html.twig', [ 'mymenu' => $mymenu , 'test' => $test] );

        }
    }
 
    public function login(Request $request , AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('menu');
        }
       // get the login error if there is one
       $error = $authenticationUtils->getLastAuthenticationError() ? $authenticationUtils->getLastAuthenticationError() : "";
       // last username entered by the user
       $lastUsername = $authenticationUtils->getLastUsername();

  
        // $this->addflash('success', 'Vous bien connecté !');
       return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'request'=> $request]);

       
        

    }
    /**
     * @Route("/login", name="main_login")
     */
    public function loginRoute(Request $request, AuthenticationUtils $authenticationUtils){
     $repo =$this->getdoctrine()->getRepository(Menu::class);
        
        $now = new \DateTime();

        $mymenu = $repo->getByDate($now);

        
        if(is_array($mymenu)){
            $mytitle       = 'Il n\'y a pas de Menu Du Jour Aujourd\'hui ';
            $mydescription = '';


        }
        else{        
            $mytitle       = $mymenu->getTitle();
            $mydescription = $mymenu->getDescription();
            
        }

        return $this->render('menu.html.twig', [
            'titre' => $mytitle, 
            'description' => $mydescription,
            'authRequired' => true,
            'logged' => true,
            'forget' => false,
            'request' => $request,
            'authenticationUtils' => $authenticationUtils
        ]);
    }
    
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, AuthenticationUtils $authenticationUtils)
    {
        $user = new User();
        

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();
            
            return  $this->forward('App\Controller\ApliController::login', [
                'request'  => $request,
                'authenticationUtils' => $authenticationUtils,
            ]);
        }

        return $this->render('security/register.html.twig', [
            'request'=> $request,
            // 'logged' => false,
            // 'authRequired' => false,
            // 'forget' => false,
            'encoder' => $encoder,
            'form' => $form->createView(),
        ]);
    }
  
    public function forgetPassword()
    {
        return $this->render('security/motDePasseOublie.html.twig', [
        ]);
    }
    /**
     * @Route("/forget", name="main_forgetPwd")
     */
    public function forgetPasswordRoute()
    {
        return $this->render('apli/menu.html.twig', [
            'logged' => false,
            'authRequired' => false,
            'forget' => true,
        ]);
    }
    

    /**
     * @Route("/forgotten_password", name="app_forgotten_password")
     */
    public function forgottenPassword(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        if ($request->isMethod('POST')) {
        
            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByMail($email);
            /* @var $user User */
            if  ($user==null) {
                $this->addFlash('danger', 'Email Inconnu, veuillez entrer Email Valide');
                 return $this->redirectToRoute('app_forgotten_password');
            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                // return $this->redirectToRoute('menu');
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Forgot Password'))
                ->setFrom('g.ponty@dev-web.io')
                ->setTo($user->getMail())
                ->setBody(
                    $this->renderView(
                        'forgotmail.html.twig',
                        ['url' => $url]),
                        'text/html');
            $mailer->send($message);
            $this->addFlash('success', 'Votre Mail est bien envoyé');

            // return $this->redirectToRoute('menu');
        }

        return $this->render('security/forgotten_password.html.twig');
    }


    /**
     * @Route("/resetpassword/{token}", name="app_reset_password",  methods={"GET", "POST"})
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {

        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Token Inconnu');
                return $this->redirectToRoute('app_forgotten_password');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour');

            // return $this->redirectToRoute('menu');

            return $this->redirectToRoute('main_login');
        }else {

            return $this->render('security/resetpassword.html.twig', ['token' => $token]);
        }

    }

   

}

