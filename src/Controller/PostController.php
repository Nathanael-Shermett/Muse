<?php

namespace App\Controller;

use App\Form\CommentType;
use App\Form\PostType;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\PostCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PostController extends AbstractController
{
	/**
	 * @param Request $request
	 * @Route("/post/new", name="new_post")
	 * @return
	 */
	public function new(Request $request, UserInterface $user)
	{
		// Get the form categories (so we can pass them to the form).
		$categories = $this->getDoctrine()->getRepository(PostCategory::class)->findAll();

		// Build the form.
		$form = $this->createForm(PostType::class, NULL, ['categories' => $categories]);

		// Handle the submission (will only happen on POST).
		$form->handleRequest($request);

		// If the form is submitted (and good to go)...
		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$data = $form->getData();

			// Data to save.
			$title = $data['title'];
			$content = $data['content'];
			$abstract= $data['abstract'];
			$categories[0] = $this->getDoctrine()->getRepository(PostCategory::class)->find($data['categories'][0]);
			$categories[1] = $this->getDoctrine()->getRepository(PostCategory::class)->find($data['categories'][1]);

			// Save the post.
			$post = new Post();
			$post->setTimestamp(new \DateTime());
			$post->setUser($user);
			$post->setTitle($title);
			$post->setContent($content);
			$post->setAbstract($abstract);
			$post->addCategories($categories[0]);
			$post->addCategories($categories[1]);

			$entityManager->persist($post);
			$entityManager->flush();

			return $this->redirectToRoute('homepage');
		}

		return $this->render('post/new.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * @param Request $request
	 * @Route("/post/{post_id}", name="view_post", requirements={"post_id"="\d+"})
	 * @Route("/post/new_comment/{post}", name="new_comment")
	 * @return
	 */
	public function view($post_id, Request $request, UserInterface $user)
	{
		// Get the post.
		$post = $this->getDoctrine()->getRepository(Post::class)->find($post_id);

		// Throw an error if the post does not exist.
		if (!$post)
		{
			throw $this->createNotFoundException("No post found for ID #$post_id.");
		}

		// Build the comment form.
		$comment = new Comment();
		$form = $this->createForm(CommentType::class, $comment);
		$comment->setTimestamp(new \DateTime());
		$comment->setPost($post);
		$comment->setUser($user);

		// Handle the submission (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid())
		{
			// Save the comment.
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($comment);
			$entityManager->flush();

			// Redirect to this page (effectively resetting form values).
			return $this->redirect($request->getUri());
		}

		// Get the comments.
		$comments = $post->getComments();

		// Render everything.
		return $this->render('post/view.html.twig', [
			'post'     => $post,
			'form'     => $form->createView(),
			'comments' => $comments,
		]);
	}
}