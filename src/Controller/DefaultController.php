<?php


namespace App\Controller;

use App\Entity\Recette;
use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Class HomeController
 * 
 * @package App\Controller
 * 
 * @Route("/api", name="home")
 * 
 * Author Vincent Labarthe 
 */
class DefaultController extends AbstractController
{
    /**
     * Permet d'afficher la totalité des recettes
     * 
     * @Route("/home")
     */
    public function defaultAction()
    {
        $recettes = $this->getDoctrine()->getRepository(Recette::class)->findAll();
        $arrayRecette = [];

        foreach ($recettes as $recette) {
            $ingredients = $recette->getIngredients();
            if ($ingredients) {
                foreach ($ingredients as $ingredient) {
                    $arrayIngredient[] = $ingredient->getName();
                }
            } 

            $arrayRecette[] = [
                'id' => $recette->getId(),
                'nom' => $recette->getName(),
                'sous-titre' => $recette->getsousTitre(),
                'ingredient' => $arrayIngredient
            ];
        }
        return new JsonResponse($arrayRecette);
    }

    /**
     * Permet de supprimer une recette via son Id
     *
     * @Route("/delete/{id}",name="delete")
     */
    public function deleteAction($id)
    {
        $recetteToDelete = $this->getDoctrine()->getRepository(Recette::class)->findOneBy(['id' => $id]);

        if ($recetteToDelete) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recetteToDelete);
            $entityManager->flush();

            return new JsonResponse('Recette supprimée');
        } else {
            return new JsonResponse('Aucune recette trouvée');
        }
    }

    /**
     * Permet de mettre à jour une recette
     *
     * @Route ("/update/{id}", name="udpdate")
     */
    public function updateAction(Request $request, $id)
    {
        $data  = $request->getContent();
        $data  = json_decode($data);
        $name = $data->name;
        $sousTitre = (isset($data->sousTitre) ? $data->sousTitre : '');
        $ingredients = $data->ingredient;

        $entityManager = $this->getDoctrine()->getManager();
        $recetteToUpdate = $this->getDoctrine()->getRepository(Recette::class)->findOneBy(['id' => $id]);
        $ingredientEntity = new Ingredient();

        if (is_array($ingredients)) {
            foreach ($ingredients as $ingredient) {
                $ingredientEntity->setName($ingredient);
                $recetteToUpdate->addIngredient($ingredientEntity);
            }
        } else {
            $ingredientEntity->setName($ingredients);
            $recetteToUpdate->addIngredient($ingredientEntity);
        }
        $entityManager->persist($ingredientEntity);

        $recetteToUpdate->setName($name);
        if ($sousTitre !== '') {
            $recetteToUpdate->setSousTitre($sousTitre);
        }

        $entityManager->flush();

        return new JsonResponse('Recette mise à jour');
    }

    /**
     * Permet de créer une recette 
     * 
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        $data  = $request->getContent();
        $data  = json_decode($data);
        $name = $data->name;
        $sousTitre = (isset($data->sousTitre) ? $data->sousTitre : '');
        $ingredients = $data->ingredient;

        $entityManager = $this->getDoctrine()->getManager();

        $newRecette = new Recette();
        $ingredientEntity = new Ingredient();

        if (is_array($ingredients)) {
            foreach ($ingredients as $ingredient) {
                $ingredientEntity->setName($ingredient);
            }
        } else {
            $ingredientEntity->setName($ingredients);
        }
        $entityManager->persist($ingredientEntity);
        $entityManager->flush();

        $newRecette->addIngredient($ingredientEntity);
        $newRecette->setName($name);
        if ($sousTitre !== '') {
            $newRecette->setSousTitre($sousTitre);
        }
        
        $entityManager->persist($newRecette);
        $entityManager->flush();
        $newRecetteId = $newRecette->getId();

        return new JsonResponse('Recette créé aevc l\'id ' . $newRecetteId);;
    }
}
