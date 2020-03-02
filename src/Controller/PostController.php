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

class PostController extends AbstractController
{
	/**
	 * @param Request $request
	 * @Route("/post/new", name="new_post")
	 * @return
	 */
	public function new(Request $request)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', 'You must be logged in to write posts on Muse.
				If you do not have an account with us, we suggest creating one.
				It is a one-time process and it is very easy.');

			return $this->redirectToRoute('homepage');
		}

		// Get the user.
		$user = $this->getUser();

		// Build the form.
		$form = $this->createForm(PostType::class, NULL);

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
			$abstract = $data['abstract'];
			$categories[0] = $this->getDoctrine()->getRepository(PostCategory::class)->find($data['categories'][0]);
			$categories[1] = $this->getDoctrine()->getRepository(PostCategory::class)->find($data['categories'][1]);

			// Save the post.
			$post = new Post();
			$post->setTimestamp(new \DateTime());
			$post->setUser($user);
			$post->setTitle($title);
			$post->setContent($content);
			$post->setAbstract($abstract);
			$post->setDeleted(FALSE);
			$post->addCategory($categories[0]);
			$post->addCategory($categories[1]);

			$entityManager->persist($post);
			$entityManager->flush();

			return $this->redirectToRoute('homepage');
		}

		return $this->render('post/edit.html.twig', [
			'form' => $form->createView(),
			'title' => 'New Post',
		]);
	}

	/**
	 * @param Request $request
	 * @Route("/post/delete/{post_id}/{csrf_token}", name="delete_post", defaults={"csrf_token"=""}, requirements={"post_id"="\d+"})
	 * @return
	 */
	public function delete($post_id, $csrf_token, Request $request)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', 'You must be logged in to delete a post.');

			return $this->redirectToRoute('homepage');
		}

		if ($this->isCsrfTokenValid("delete-post-$post_id", $csrf_token))
		{
			$entityManager = $this->getDoctrine()->getManager();
			$post = $entityManager->getRepository(Post::class)->find($post_id);
			$post->setDeleted(TRUE);
			$entityManager->flush();

			$this->addFlash('success', "The post has been deleted.");

			return $this->redirectToRoute('homepage');
		}
		else
		{
			$this->addFlash('error', "
				An unauthorized attempt was made to delete this post, but we intercepted it.
				You are most likely receiving this message because you clicked a link you shouldn't have.
				If you believe you are receiving this message in error, please try again.");

			return $this->redirectToRoute('view_post', ['post_id' => $post_id]);
		}
	}

	/**
	 * @param Request $request
	 * @Route("/post/edit/{post_id}", name="edit_post", requirements={"post_id"="\d+"})
	 * @return
	 */
	public function edit($post_id, Request $request)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', 'You must be logged in to edit a post.');

			return $this->redirectToRoute('homepage');
		}

		// Get the user.
		$user = $this->getUser();

		// Get the post to be edited.
		$entityManager = $this->getDoctrine()->getManager();
		$post = $entityManager->getRepository(Post::class)->find($post_id);

		// Build the form.
		$form = $this->createForm(PostType::class, $post);

		// Handle the submission (will only happen on POST).
		$form->handleRequest($request);

		// If the form is submitted (and good to go)...
		if ($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			// Data to save.
			$title = $data->getTitle();
			$content = $data->getContent();
			$abstract = $data->getAbstract();
			$categories = $data->getCategories();

			// Save the post.
			$post->setTitle($title);
			$post->setContent($content);
			$post->setAbstract($abstract);

			foreach ($categories as $category)
			{
				$post->addCategory($category);
			}

			$entityManager->persist($post);
			$entityManager->flush();

			return $this->redirectToRoute('view_post', ['post_id' => $post_id]);
		}

		return $this->render('post/edit.html.twig', [
			'form' => $form->createView(),
			'title' => 'Edit Post',
		]);
	}

	/**
	 * @param Request $request
	 * @Route("/post/{post_id}", name="view_post", requirements={"post_id"="\d+"})
	 * @Route("/post/new_comment/{post}", name="new_comment")
	 * @return
	 */
	public function view($post_id, Request $request)
	{
		// Get the post.
		$post = $this->getDoctrine()->getRepository(Post::class)->find($post_id);

		if ($post->deleted())
		{
			$this->addFlash('error', 'The post you are trying to view has been deleted and can no longer be viewed.');

			return $this->redirectToRoute('homepage');
		}

		// Throw an error if the post does not exist.
		if (!$post)
		{
			$this->addFlash('error', 'The post you are trying to view does not exist.');

			return $this->redirectToRoute('homepage');
		}

		// Get the comments.
		$comments = $post->getComments();

		// If the user is logged in, build the comment form.
		if ($this->getUser())
		{
			// Get the user.
			$user = $this->getUser();

			// Build the comment form.
			$comment = new Comment();
			$form = $this->createForm(CommentType::class, $comment);
			$comment->setTimestamp(new \DateTime());
			$comment->setPost($post);
			$comment->setUser($user);
			$comment->setDeleted(FALSE);

			// Handle the submission (will only happen on POST)
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid())
			{
				// Save the comment.
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($comment);
				$entityManager->flush();

				// Show a success message.
				$this->addFlash('success', 'Your comment has been posted.');

				// Redirect to this page (effectively resetting form values).
				return $this->redirect($request->getUri());
			}

			// Render everything.
			return $this->render('post/view.html.twig', [
				'post' => $post,
				'form' => $form->createView(),
				'comments' => $comments,
			]);
		}
		else
		{
			// Render everything.
			return $this->render('post/view.html.twig', [
				'post' => $post,
				'comments' => $comments,
			]);
		}
	}
}