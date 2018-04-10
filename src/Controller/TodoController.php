<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Entity\User;
use App\Form\TodoType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TodoController extends Controller
{
    /**
     * @Route("/", name="todoListAll")
     */
    public function listAllAction()
    {
    	$repository = $this->getDoctrine()->getRepository(Todo::class);
    	$todos = $repository->findActiveTodosList($this->getUser());
        return $this->render('todo/index.html.twig', [
            'todos' => $todos
        ]);
    }

    /**
     * @Route("todo/create", name="todoCreate")
     */
    public function createAction(Request $request)
    {
    	$todo = new Todo();
    	$todo->setUser($this->getUser());

    	$form = $this->createForm(TodoType::class, $todo, []);

    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid())
    	{
    		$em = $this->getDoctrine()->getManager();

    		$em->persist($todo);

    		$em->flush();

    		$this->addFlash('success', 'Todo created successfully!');

    		return $this->redirectToRoute('todoListAll');
    	}

    	return $this->render('todo/create.html.twig', [
    		'form' => $form->createView(),
    	]);
    }

    /**
     * @Route("todo/edit/{id}", name="todoEdit")
     */
    public function editAction(Request $request, Todo $todo)
    {
    	$form = $this->createForm(TodoType::class, $todo);

    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid())
    	{
    		$em = $this->getDoctrine()->getManager();

    		$em->flush();

    		$this->addFlash('success', 'Todo edited successfully');

    		return $this->redirectToRoute('todoListAll');
    	}

    	return $this->render('todo/edit.html.twig', [
    		'form' => $form->createView(),
    	]);
    }

    /**
     * @Route("todo/delete/{id}", name="todoDelete")
     */
    public function deleteAction(Request $request, Todo $todo)
    {
    	if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('todoListAll');
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($todo);

        $em->flush();

        $this->addFlash('success', 'Todo deleted successfully');

        return $this->redirectToRoute('todoListAll');

    }
}
