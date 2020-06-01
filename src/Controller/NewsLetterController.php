<?php

namespace App\Controller;
use App\Entity\Menu;
use App\Entity\User;
use App\Entity\Commande;
use App\Entity\Newsletter;
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

class NewsLetterController extends AbstractController{

    /** 
     * @Route("/newsletter", name="newsletter")
     */
    public function recupNewsLetter()
    {
        $repository = $this->getDoctrine()->getRepository(Newsletter::class);

        $newsletter = $repository->findBy(array('archive' => 0));
        return $this->render('newsLetter.html.twig', [
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * @Route("/sendnewsletter/{token}", name="newsletter_send")
     */
    public function sendNewsLetter(Request $request, string $token, \Swift_Mailer $mailer)
    {
        $repository         = $this->getDoctrine()->getRepository(Newsletter::class);
        $newsletterToSend   = $repository->findById($token);

        $userRepository     = $this->getDoctrine()->getRepository(User::class);
        $allUserSub         = $userRepository->findBy(array('newsletter' => 1));
        
        
        if (empty($newsletterToSend)){
            return  $this->forward('App\Controller\NewsLetterController::recupNewsLetter', [
            
                ]);
        }
        $newsletterToSend = $newsletterToSend[0];

        $destinataires      =   []; /* ['Email' => 'nom prenom']*/
        foreach ($allUserSub as $oneUser){
            $destinataires[$oneUser->getMail()] = $oneUser->getName() . " " . $oneUser->getLastName();
        }
    
        unset($allUserSub);

        $message = (new \Swift_Message($newsletterToSend->getTitre()))
                ->setFrom('g.ponty@dev-web.io')
                ->setTo($destinataires)
                ->setBody(
                    $this->renderView(
                        'vueMailNewsletter.html.twig',
                        [
                            'titre' => $newsletterToSend->getTitre(),
                            'content' => $newsletterToSend->getDescription(),
                            'titremenu' => $newsletterToSend->getMenu()->getTitle(),
                            'contentmenu' => $newsletterToSend->getMenu()->getDescription(),
                        ]
                    ),
                    'text/html'
                );
                $mailer->send($message);
        $this->addFlash("success", "Newsletter envoyée");
        return $this->redirectToRoute("newsletter");
    }

    /**
     * @Route("/archivenewsletter/{token}", name="newsletter_archive")
     */
    public function archiveNewsLetter(Request $request, string $token, ObjectManager $manager)
    {
        $repository         = $this->getDoctrine()->getRepository(Newsletter::class);
        $newsletterToSend   = $repository->findById($token);


        if (empty($newsletterToSend)){
            return  $this->forward('App\Controller\NewsLetterController::recupNewsLetter', [
            
                ]);
        }
        $newsletterToSend = $newsletterToSend[0];

        $newsletterToSend->setArchive(true);
        $manager->flush();

        return $this->redirectToRoute("newsletter");
    }

    
}