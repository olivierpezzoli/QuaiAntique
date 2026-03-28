<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DishRepository;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardAndMenusController extends AbstractController
{
    #[Route('/cardandmenus', name: 'app_card_and_menus')]
    public function index(DishRepository $dishRepository, MenuRepository $menuRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('card_and_menus/index.html.twig', [
            'dishes_dish' => $dishRepository->getAllActiveDish(),
            'dishes_menu' => $menuRepository->getAllActiveMenu(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
