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

class CommentController extends AbstractController
{
	/**
	 * Deletes a comment.
	 *
	 * @param int                 $comment_id
	 * @param string              $csrf_token
	 * @param Request             $request
	 * @param TranslatorInterface $t
	 * @Route("/comment/delete/{comment_id}/{csrf_token}", name="delete_comment", defaults={"csrf_token"=""}, requirements={"comment_id"="\d+"})
	 */
	public function delete($comment_id, $csrf_token, Request $request, TranslatorInterface $t)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', $t->trans('comment.delete.must_be_logged_in'));

			return $this->redirectToRoute('homepage');
		}

		// The logged-in user.
		$user = $this->getUser();

		$entityManager = $this->getDoctrine()->getManager();
		$comment = $entityManager->getRepository(Comment::class)->find($comment_id);

		// Throw an error if the comment does not exist.
		if (!$comment)
		{
			$this->addFlash('error', $t->trans('comment.delete.does_not_exist'));

			return $this->redirectToRoute('homepage');
		}

		// Get the post the comment belongs to.
		$post = $comment->getPost();

		// Throw an error if the post does not exist.
		if (!$post)
		{
			$this->addFlash('error', $t->trans('comment.delete.orphaned'));

			return $this->redirectToRoute('homepage');
		}

		if ($comment->deleted())
		{
			$this->addFlash('error', $t->trans('comment.delete.already_deleted'));

			return $this->redirectToRoute('homepage');
		}

		if ($post->deleted())
		{
			$this->addFlash('error', $t->trans('comment.delete.post_deleted'));

			return $this->redirectToRoute('homepage');
		}

		if ($this->isCsrfTokenValid("delete-comment-$comment_id", $csrf_token))
		{
			// If the comment belongs to an administrator and a moderator is trying to delete it.
			if ($comment->getUser()->hasRole('ROLE_ADMIN')
				&& !$user->hasRole('ROLE_ADMIN')
				&& $user->hasRole('ROLE_MODERATOR'))
			{
				$this->addFlash('error', $t->trans('comment.delete.only_administrators_can_delete_other_administrators'));

				return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
			}

			// If the person trying to delete this comment is the comment's author, or a moderator.
			elseif ($user->getId() == $comment->getUser()->getId() || $user->hasRole('ROLE_MODERATOR'))
			{
				$comment->setDeleted(TRUE);
				$entityManager->flush();

				$this->addFlash('success', $t->trans('comment.delete.success'));

				return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
			}

			// Invalid deletion attempt.
			else
			{
				$this->addFlash('error', $t->trans('comment.delete.not_authorized'));

				return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
			}
		}
		else
		{
			$this->addFlash('error', $t->trans('comment.delete.csrf_invalid'));

			return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
		}
	}

	/**
	 * Allows a comment to be edited. Also edits the comment on form submit.
	 *
	 * @param int                 $comment_id
	 * @param Request             $request
	 * @param TranslatorInterface $t
	 * @Route("/comment/edit/{comment_id}", name="edit_comment", requirements={"comment_id"="\d+"})
	 */
	public function edit($comment_id, Request $request, TranslatorInterface $t)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', $t->trans('comment.edit.must_be_logged_in'));

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
			$this->addFlash('error', $t->trans('comment.edit.does_not_exist'));

			return $this->redirectToRoute('homepage');
		}

		// Get the post the comment belongs to.
		$post = $comment->getPost();

		// Throw an error if the post does not exist.
		if (!$post)
		{
			$this->addFlash('error', $t->trans('comment.edit.orphaned'));

			return $this->redirectToRoute('homepage');
		}

		if ($comment->deleted())
		{
			$this->addFlash('error', $t->trans('comment.edit.already_deleted'));

			return $this->redirectToRoute('homepage');
		}

		if ($post->deleted())
		{
			$this->addFlash('error', $t->trans('comment.edit.post_deleted'));

			return $this->redirectToRoute('homepage');
		}

		// If the comment belongs to an administrator and a moderator is trying to delete it.
		if ($comment->getUser()->hasRole('ROLE_ADMIN')
			&& !$user->hasRole('ROLE_ADMIN')
			&& $user->hasRole('ROLE_MODERATOR'))
		{
			$this->addFlash('error', $t->trans('comment.edit.only_administrators_can_edit_other_administrators'));

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
			$this->addFlash('error', $t->trans('comment.edit.not_authorized'));

			return $this->redirectToRoute('view_post', ['post_id' => $post->getId()]);
		}
	}
}