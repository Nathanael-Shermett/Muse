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
		$entityManager = $this->getDoctrine()->getManager();
		$comment = $entityManager->getRepository(Comment::class)->find($comment_id);

		if ($this->isCsrfTokenValid("delete-comment-$comment_id", $csrf_token))
		{
			$comment->setDeleted(TRUE);
			$entityManager->flush();

			$this->addFlash('success', "The comment has been deleted.");

			return $this->redirectToRoute('view_post', ['post_id' => $comment->getPost()->getId()]);
		}
		else
		{
			$this->addFlash('error', "
				An unauthorized attempt was made to delete a comment, but we intercepted it.
				You are most likely receiving this message because you clicked a link you shouldn't have.
				If you believe you are receiving this message in error, please try again.");

			return $this->redirectToRoute('view_post', ['post_id' => $comment->getPost()->getId()]);
		}
	}

	/**
	 * @param Request $request
	 * @Route("/comment/edit/{comment_id}", name="edit_comment", requirements={"comment_id"="\d+"})
	 * @return
	 */
	public function edit($comment_id, Request $request)
	{
		$entityManager = $this->getDoctrine()->getManager();

		// Get the user.
		$user = $this->getUser();

		// Get the comment.
		$comment = $entityManager->getRepository(Comment::class)->find($comment_id);

		// Throw an error if the comment does not exist.
		if (!$comment)
		{
			throw $this->createNotFoundException("No comment found for ID #$comment_id.");
		}

		// Get the post the comment belongs to.
		$post = $comment->getPost();

		// Throw an error if the post does not exist.
		if (!$post)
		{
			throw $this->createNotFoundException("No post found for comment ID #$comment_id.");
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
}