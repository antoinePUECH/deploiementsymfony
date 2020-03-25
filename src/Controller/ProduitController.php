<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(Request $request)
    {

        $em = $this -> getDoctrine() -> getManager();

        $produit = new Produit();

        $form = $this -> createForm(ProduitType::class, $produit);

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){

            $fichier = $form->get('photo')->getData();

            if($fichier){
                $nomFichier = uniqid() .'.'. $fichier->guessExtension();
                try{
                    $fichier->move(
                        $this->getParameter('upload_dir'),
                        $nomFichier
                    );
                }
                catch(FileException $e){
                    $this->addFlash("danger", "l'image marche pas frere",
                 );
                    return $this->redirectToRoute('produit');
                }

                $produit->setPhoto($nomFichier);
            }

            $em -> persist($produit);
            $em -> flush();

            $this->addFlash("success", "Produit ajouté");
        }

        $produits = $em -> getRepository(Produit::class)->findAll();
        
        return $this->render('Produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits,
            'form_produit_new' => $form -> createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}", name="mon_produit")
     */

     public function produit(Request $request, Produit $produit=null) {
            if($produit != null){
                // le produit existe
                $form = $this->createForm(ProduitType::class, $produit);
                $form -> handleRequest($request);

                if($form -> isSubmitted() && $form -> isValid()) {
                    $em = $this -> getDoctrine() -> getManager();
                    $em -> persist($produit);
                    $em -> flush();
                }
                return $this -> render('Produit/produit.html.twig', [
                    'produit' => $produit,
                    'form' => $form->createView()
                ]);
            }
            else{
                //le produit n'existe pas, on redirige l'alternaute
                return $this -> redirectToRoute('produit');
            }
     }

     /**
     * @Route ("produit/delete/{id}", name="delete_produit")
     */
    public function delete(Produit $produit=null){
        if($produit!=null){
            $pdo = $this-> getDoctrine()->getManager();
            $pdo -> remove($produit);
            $pdo -> flush();

            $this->addFlash("success", "Produit supprimé");
        }
        else {
            $this->addFlash("danger", "Produit introuvable");
        }
        return $this -> redirectToRoute('produit');
    }
}
