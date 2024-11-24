<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TodoTask;
use App\Form\TaskFormType;
use App\Form\TaskListFormType;
use App\Repository\TodoTaskRepository;
use DateTimeImmutable;

#[IsGranted('ROLE_USER')]
class TasksController extends AbstractController
{
    #[Route('/', name: 'app_tasks',methods: ['GET', 'POST'])]
    public function index(Request $request, TodoTaskRepository $todoTaskRepository, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        //get tasks owned by the user
        $tasks = $entityManager->getRepository(TodoTask::class)->findByOwner($user->getId());

        //if no tasks found redirect
        //if (empty($tasks)) return $this->redirectToRoute('app_new_task');
    
        // create the form
        $form = $this->createForm(TaskListFormType::class, ['tasks' => $tasks]);
    
        // handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // update each task item
            foreach ($form->get('tasks')->getData() as $task) {
                $entityManager->persist($task);
            }
            //save
            $entityManager->flush();
    
            return $this->redirectToRoute('app_tasks');
        }
    
        // render the template
        return $this->render('tasks/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new_task', name: 'app_new_task', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManagerInterface): Response 
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        //instantiate new task
        $task = new TodoTask();

        // create the form
        $form = $this->createForm(TaskFormType::class, $task);

        // handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //set owner
            $task->setUser($user);
            //set created_at, updated_at properties
            $task->setCreatedAt(new DateTimeImmutable());
            $task->setUpdatedAt(new DateTimeImmutable());

            //date is nullable
            $date = $form->get('date')->getData();
            //set date property
            if ($date != null) $task->setDate(DateTimeImmutable::createFromMutable($date));

            $entityManagerInterface->persist($task);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_tasks');
        }

        return $this->render('tasks/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/task/edit/{id}', name: 'app_update_task', methods: ['GET', 'POST'])]
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        //get task by id
        $task = $entityManagerInterface->getRepository(TodoTask::class)->find($id);

        //check if task found
        if (!$task) {
            throw $this->createNotFoundException(
                'No task found for id '.$id
            );
        }
        //check if user owns the task
        if ($user != $task->getUser()) {
            throw $this->createAccessDeniedException(
                'Access denied for id' . $id
            );
        }

        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //update 'updated_at' column
            $task->setUpdatedAt(new DateTimeImmutable());

            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_tasks');
        }

        return $this->render('tasks/update.html.twig', [
            'form' => $form,
        ]);
    }
    
    #[Route('/task/delete/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManagerInterface, int $id): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        //get task by id
        $task = $entityManagerInterface->getRepository(TodoTask::class)->find($id);

        //check if task found
        if (!$task) {
            throw $this->createNotFoundException(
                'No task found for id '.$id
            );
        }

        //check if user owns the task
        if ($user != $task->getUser()) {
            throw $this->createAccessDeniedException(
                'Access denied for id' . $id
            );
        }

        $entityManagerInterface->remove($task);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('app_tasks');
    }

    // #[Route('/task/mark_finished/{id}', name: 'app_update_task_as_finished', methods: ['POST'])]
    // public function updateTaskFinished(Request $request, EntityManagerInterface $entityManagerInterface, int $id): Response
    // {
    //     /** @var \App\Entity\User $user */
    //     $user = $this->getUser();
        
    //     //get task by id
    //     $task = $entityManagerInterface->getRepository(TodoTask::class)->find($id);

    //     //check if task found
    //     if (!$task) {
    //         throw $this->createNotFoundException(
    //             'No task found for id '.$id
    //         );
    //     }

    //     //check if user owns the task
    //     if ($user != $task->getUser()) {
    //         throw $this->createAccessDeniedException(
    //             'Access denied for id' . $id
    //         );
    //     }

    //     //flip finished property
    //     if (!$task->getFinished()){
    //         $task->setFinished(true);
    //     } else {
    //         $task->setFinished(false);
    //     }

    //     $entityManagerInterface->flush();

    //     return $this->redirectToRoute('app_tasks');
    // }
}
