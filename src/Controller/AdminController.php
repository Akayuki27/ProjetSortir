<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{

    #[Route('/villes', name: '_villes')]
    public function list(VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findAll();
        return $this->render('admin/villes.html.twig', [
            'villes'=>$villes
        ]);
    }
}
