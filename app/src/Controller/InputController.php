<?php

namespace App\Controller;

use App\Entity\Activity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InputController extends AbstractController
{   
    /**
     * @Route("/get-all", name="get_all", methods="GET")
     */
    public function getAll(ManagerRegistry $doctrine): JsonResponse
    {   
        $responseArray = array();
        $activities = $doctrine->getRepository(Activity::class)->findAll();

        // if (!$activities) {
        //     print("nothing here mate!");
        //     throw $this->createNotFoundException(
        //         'No activity found'
        //     );
        // }
        foreach ($activities as $activity) {
            array_push($responseArray, array($activity->getId(), $activity->getName(), $activity->getDuration()));
        }
        // echo $activity;
        // print($activity[0]->getName());
        
        // if (!$activities) { return new JsonResponse($responseArray);}
        
        return new JsonResponse($responseArray);
        // $values = array(
        //     array('Workout', 60),
        //     array('Bake a cake', 30)
        // );
        // return new JsonResponse($values);

    }
    /**
     * @Route("/add", name="add", methods="POST")
     */

    // gonna handle database connection decoding and saving to to database
    public function save(ManagerRegistry $doctrine, Request $request): Response
    {   
        // $request = Request::createFromGlobals();

        $name = $request->get('name');
        $duration = $request->get('duration');
        $entityManager = $doctrine->getManager();
        $activity = new Activity();
        
        $activity->setName($name);
        $activity->setDuration($duration);
        
        $entityManager->persist($activity);   
        $entityManager->flush();
        return new Response('Saved new activity'. $name. $duration. "with the id ==>" . $activity->getId());
    }

    /**
     * @Route("/delete/{id}", name="delete", methods="DELETE")
     */

    // gonna handle database connection decoding and deleteing from database
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $activity = $entityManager->getRepository(Activity::class)->find($id);
     
        if (!$activity) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($activity);
        $entityManager->flush();
        
        return new JsonResponse($activity->getName());
        // return new Response('Deleted the activity');
    }
}
