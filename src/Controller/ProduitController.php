<?php

namespace App\Controller;

use App\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit;
use App\Form\PanierType;
use App\Form\ProduitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Contracts\Translation\TranslatorInterface;

    /**
     * @Route("/{_locale}")
     */
class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(Request $request, TranslatorInterface $translator)
    {

        $pdo = $this->getDoctrine()->getManager();
        $produits = $pdo->getRepository(Produit::class)->findAll();

        $produit = new Produit();
         $form = $this->createForm(ProduitType::class, $produit);

         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
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
                    $this->addFlash("danger", $translator->trans('impossible.image'));
                    return $this->redirectToRoute('produit');
                }

                $produit->setPhoto($nomFichier);
            }
             $pdo->persist($produit);
             $pdo->flush();

             $this->addFlash("success", $translator->trans('produit.ajoute'));
         }
         
        return $this->render('produit/index.html.twig', [
            'produit' => $produits,
            'new_produit_form' => $form->createView()
        ]);
    
}

    /**
     * @Route("/produit/{id}", name="un_produit")
     */

    public function produit(Request $request,  Produit $produit=null, TranslatorInterface $translator){
        if($produit != null){

            $panier = new Panier();
            $pdo = $this->getDoctrine()->getManager();
            $form = $this->createForm(PanierType::class, $panier);
       
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $panier->setDateAjout(new \DateTime('now'));
            $panier->setProduit($produit);
            $panier->setEtat(False);
            $pdo->persist($panier); 
            $pdo->flush(); 
            $this->addFlash("success", $translator->trans('produits.ajoute'));
            return $this->redirectToRoute('panier');
        }
        return $this->render('produit/produit.html.twig', [
            'produit' => $produit,
            'ajout_article' => $form->createView(),
        ]);
    }

    else{       
        $this->addFlash("danger","Impossible");

    }
}


    /**
     * @Route("/produit/delete/{id}", name="delete_produit")
     */

    public function delete(Produit $produit=null,TranslatorInterface $translator){
        if($produit != null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($produit);
            $pdo->flush();

            $this->addFlash("success", $translator->trans('produit.supprimer'));
        }
        else{
            $this->addFlash("danger", $translator->trans('produit.introuvable'));
        }
            return $this->redirectToRoute('produit');
       }
}
