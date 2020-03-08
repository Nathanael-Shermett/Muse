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
use Symfony\Contracts\Translation\TranslatorInterface;

class PostController extends AbstractController
{
	/**
	 * @param Request $request
	 * @Route("/post/new", name="new_post")
	 * @return
	 */
	public function new(Request $request, TranslatorInterface $t)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', $t->trans('post.new.must_be_logged_in'));

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
	public function delete($post_id, $csrf_token, Request $request, TranslatorInterface $t)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', $t->trans('post.delete.must_be_logged_in'));

			return $this->redirectToRoute('homepage');
		}

		// The logged-in user.
		$user = $this->getUser();

		$entityManager = $this->getDoctrine()->getManager();
		$post = $entityManager->getRepository(Post::class)->find($post_id);

		if ($this->isCsrfTokenValid("delete-post-$post_id", $csrf_token))
		{
			// If the post belongs to an administrator and a moderator is trying to delete it.
			if ($post->getUser()->hasRole('ROLE_ADMIN')
				&& !$user->hasRole('ROLE_ADMIN')
				&& $user->hasRole('ROLE_MODERATOR'))
			{
				$this->addFlash('error', $t->trans('post.delete.only_administrators_can_delete_other_administrators'));

				return $this->redirectToRoute('view_post', ['post_id' => $post_id]);
			}

			// If the person trying to delete this post is the post's author, or a moderator.
			elseif ($user->getId() == $post->getUser()->getId() || $user->hasRole('ROLE_MODERATOR'))
			{
				$post->setDeleted(TRUE);
				$entityManager->flush();
				$this->addFlash('success', $t->trans('post.delete.success'));

				return $this->redirectToRoute('homepage');
			}

			// Invalid deletion attempt.
			else
			{
				$this->addFlash('error', $t->trans('post.delete.not_authorized'));

				return $this->redirectToRoute('view_post', ['post_id' => $post_id]);
			}
		}
		else
		{
			$this->addFlash('error', $t->trans('post.delete.csrf_error'));

			return $this->redirectToRoute('view_post', ['post_id' => $post_id]);
		}
	}

	/**
	 * @param Request $request
	 * @Route("/post/edit/{post_id}", name="edit_post", requirements={"post_id"="\d+"})
	 * @return
	 */
	public function edit($post_id, Request $request, TranslatorInterface $t)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', $t->trans('post.edit.must_be_logged_in'));

			return $this->redirectToRoute('homepage');
		}

		// The logged-in user.
		$user = $this->getUser();

		// Get the post to be edited.
		$entityManager = $this->getDoctrine()->getManager();
		$post = $entityManager->getRepository(Post::class)->find($post_id);

		// If the post belongs to an administrator and a moderator is trying to delete it.
		if ($post->getUser()->hasRole('ROLE_ADMIN')
			&& !$user->hasRole('ROLE_ADMIN')
			&& $user->hasRole('ROLE_MODERATOR'))
		{
			$this->addFlash('error', $t->trans('post.edit.only_administrators_can_edit_other_administrators'));

			return $this->redirectToRoute('view_post', ['post_id' => $post_id]);
		}

		// If the person trying to delete this post is the post's author, or a moderator.
		elseif ($user->getId() == $post->getUser()->getId() || $user->hasRole('ROLE_MODERATOR'))
		{
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

				$this->addFlash('success', $t->trans('post.edit.success'));

				return $this->redirectToRoute('view_post', ['post_id' => $post_id]);
			}

			return $this->render('post/edit.html.twig', [
				'form' => $form->createView(),
				'title' => 'Edit Post',
			]);
		}

		// Invalid edit attempt.
		else
		{
			$this->addFlash('error', $t->trans('post.edit.not_authorized'));

			return $this->redirectToRoute('view_post', ['post_id' => $post_id]);
		}
	}

	/**
	 * @param Request $request
	 * @Route("/post/{post_id}", name="view_post", requirements={"post_id"="\d+"})
	 * @Route("/post/new_comment/{post}", name="new_comment")
	 * @return
	 */
	public function view($post_id, Request $request, TranslatorInterface $t)
	{
		// Get the post.
		$post = $this->getDoctrine()->getRepository(Post::class)->find($post_id);

		if ($post->deleted())
		{
			$this->addFlash('error', $t->trans('post.view.does_not_exist'));

			return $this->redirectToRoute('homepage');
		}

		// Throw an error if the post does not exist.
		if (!$post)
		{
			$this->addFlash('error', $t->trans('post.view.does_not_exist'));

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
				$this->addFlash('success', $t->trans('comment.new.success'));

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