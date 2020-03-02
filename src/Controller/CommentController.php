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

class CommentController extends AbstractController
{
	/**
	 * @param Request $request
	 * @Route("/comment/delete/{comment_id}/{csrf_token}", name="delete_comment", defaults={"csrf_token"=""}, requirements={"comment_id"="\d+"})
	 * @return
	 */
	public function delete($comment_id, $csrf_token, Request $request)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', 'You must be logged in to delete a comment.');

			return $this->redirectToRoute('homepage');
		}

		// The logged-in user.
		$user = $this->getUser();

		$entityManager = $this->getDoctrine()->getManager();
		$comment = $entityManager->getRepository(Comment::class)->find($comment_id);

		// Throw an error if the comment does not exist.
		if (!$comment)
		{
			$this->addFlash('error', 'The comment you are attempting to delete does not exist.');

			return $this->redirectToRoute('homepage');
		}

		// Get the post the comment belongs to.
		$post = $comment->getPost();

		// Throw an error if the post does not exist.
		if (!$post)
		{
			$this->addFlash('error', 'The comment you are attempting to delete does not seem to correspond with an actual post. Therefore, it cannot be deleted.');

			return $this->redirectToRoute('homepage');
		}

		if ($comment->deleted())
		{
			$this->addFlash('error', 'The comment you are trying to delete has actually already been deleted. No further action is necessary on your part.');

			return $this->redirectToRoute('homepage');
		}

		if ($post->deleted())
		{
			$this->addFlash('error', 'The comment you are trying to delete belongs to a post that has been deleted, and as such the comment cannot be removed.');

			return $this->redirectToRoute('homepage');
		}

		if ($this->isCsrfTokenValid("delete-comment-$comment_id", $csrf_token))
		{
			// If the comment belongs to an administrator and a moderator is trying to delete it.
			if ($comment->getUser()->hasRole('ROLE_ADMIN')
				&& !$user->hasRole('ROLE_ADMIN')
				&& $user->hasRole('ROLE_MODERATOR'))
			{
				$this->addFlash('error', "Only administrators are allowed to delete other administrators' comments.");

				return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
			}

			// If the person trying to delete this comment is the comment's author, or a moderator.
			elseif ($user->getId() == $comment->getUser()->getId() || $user->hasRole('ROLE_MODERATOR'))
			{
				$comment->setDeleted(TRUE);
				$entityManager->flush();

				$this->addFlash('success', "The comment has been deleted.");

				return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
			}

			// Invalid deletion attempt.
			else
			{
				$this->addFlash('error', 'You are not authorized to delete this comment.');

				return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
			}
		}
		else
		{
			$this->addFlash('error', "
				An unauthorized attempt was made to delete a comment, but we intercepted it.
				You are most likely receiving this message because you clicked a link you shouldn't have.
				If you believe you are receiving this message in error, please try again.");

			return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
		}
	}

	/**
	 * @param Request $request
	 * @Route("/comment/edit/{comment_id}", name="edit_comment", requirements={"comment_id"="\d+"})
	 * @return
	 */
	public function edit($comment_id, Request $request)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', 'You must be logged in to edit a comment.');

			return $this->redirectToRoute('homepage');
		}

		// Get the logged-in user.
		$user = $this->getUser();

		// Get the comment to be edited.
		$entityManager = $this->getDoctrine()->getManager();
		$comment = $entityManager->getRepository(Comment::class)->find($comment_id);

		// Throw an error if the comment does not exist.
		if (!$comment)
		{
			$this->addFlash('error', 'The comment you are attempting to edit does not exist.');

			return $this->redirectToRoute('homepage');
		}

		// Get the post the comment belongs to.
		$post = $comment->getPost();

		// Throw an error if the post does not exist.
		if (!$post)
		{
			$this->addFlash('error', 'The comment you are attempting to edit does not seem to correspond with an actual post. Therefore, it cannot be edited.');

			return $this->redirectToRoute('homepage');
		}

		if ($comment->deleted())
		{
			$this->addFlash('error', 'The comment you are trying to edit has been deleted and can no longer be changed.');

			return $this->redirectToRoute('homepage');
		}

		if ($post->deleted())
		{
			$this->addFlash('error', 'The comment you are trying to edit belongs to a post that has been deleted, and as such the comment cannot be changed.');

			return $this->redirectToRoute('homepage');
		}

		// If the comment belongs to an administrator and a moderator is trying to delete it.
		if ($comment->getUser()->hasRole('ROLE_ADMIN')
			&& !$user->hasRole('ROLE_ADMIN')
			&& $user->hasRole('ROLE_MODERATOR'))
		{
			$this->addFlash('error', "Only administrators are allowed to edit other administrators' comments.");

			return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
		}

		// If the person trying to edit this comment is the comment's author, or a moderator.
		elseif ($user->getId() == $comment->getUser()->getId() || $user->hasRole('ROLE_MODERATOR'))
		{
			// Build the comment form.
			$form = $this->createForm(CommentType::class, $comment);

			// Handle the submission (will only happen on POST)
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid())
			{
				$data = $form->getData();

				// Data to save.
				$content = $data->getContent();

				// Save the post.
				$comment->setContent($content);

				$entityManager->persist($comment);
				$entityManager->flush();

				// Redirect to this page (effectively resetting form values).
				return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
			}

			// Get the comments.
			$comments = $post->getComments();

			// Render everything.
			return $this->render('comment/edit.html.twig', [
				'post' => $post,
				'form' => $form->createView(),
				'comments' => $comments,
			]);
		}

		// Invalid edit attempt.
		else
		{
			$this->addFlash('error', 'You are not authorized to edit this comment.');

			return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
		}
	}
}