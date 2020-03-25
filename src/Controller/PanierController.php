<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $pdo = $this -> getDoctrine()->getManager();

        $panier = new Panier();

        $form = $this -> createForm(PanierType::class, $panier);

        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form->isValid()){
            //le formulaire a été envoyé, on le sauvegarde
            $pdo->persist($panier); //prepare
            $pdo->flush(); //execute

            $this->addFlash("success", "Catégorie ajoutée");
        }

        $paniers = $pdo ->getRepository(Panier::class)->findAll();

        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'form_panier' => $form->createView(),
        ]);
    }
    /**
     * @Route ("delete/{id}", name="delete_panier")
     */
    public function delete(Panier $panier=null){
        if($panier!=null){
            $pdo = $this-> getDoctrine()->getManager();
            $pdo -> remove($panier);
            $pdo -> flush();

            $this->addFlash("success", "Produit supprimé");
        }
        else {
            $this->addFlash("danger", "Produit introuvable");
        }
        return $this -> redirectToRoute('home');
    }
}
