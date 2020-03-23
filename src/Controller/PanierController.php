<?php

namespace App\Controller;

use App\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PanierController extends AbstractController
{

          /**
     * @Route("/", name="panier")
     */
    public function index()
    {
     $pdo = $this->getDoctrine()->getManager();
     $paniers = $pdo->getRepository(Panier::class)->findAll();

        return $this->render('panier/index.html.twig', [
            'panier' => $paniers
        ]);
    }
     /**
     * @Route("/panier/panier{id}_delete", name="panier_delete")
     */
    public function delete(Panier $panier=null, TranslatorInterface $translator){
        if($panier != null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($panier);
            $pdo->flush();
            
        $this->addFlash('success', $translator->trans('produits.supprimer'));
    }
return $this->redirectToRoute('panier');

}
}


