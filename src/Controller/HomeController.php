<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\WelcomeType;
use App\Model\WelcomeModel;
use App\Service\DatabaseService;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PhotoRepository $photoRepository): Response
    {
        $photos = $photoRepository->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'photos' => $photos,
        ]);
    }

    #[Route('/welcome', name: 'app_welcome')]
    public function welcome(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {

        if ($userRepository->countUser() !== 0) {
            return $this->redirectToRoute('app_home');
        }
            $welcomeForm = $this->createForm(WelcomeType::class, new WelcomeModel);

            $welcomeForm->handleRequest($request);

            if ($welcomeForm->isSubmitted() && $welcomeForm->isValid()) {
                /** @var welcomeModel $data */
                $data = $welcomeForm->getData();
                $mail = $data->getEmail();

                $user = new User();
                $user->setEmail($mail);
                $user->setRoles(['ROLE_ADMIN']);
                $user->setPassword($passwordHasher->hashPassword($user, $data->getPassword()));
                $user->setIsVerified(true);
                $user->setLastName($data->getLastName());
                $user->setDefaultNbPlaces(1);

                $entityManager->persist($user);

                $entityManager->flush();

                return $this->redirectToRoute('app_home');

            }



        return $this->render('home/welcome.html.twig', [
            'welcomeForm' => $welcomeForm->createView()
        ]);
    }

}