<?php

// src/Controller/UserController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\EditProfileType;
use App\Form\RegistrationFormType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
	/**
	 * Allows users to be edited. Also edits the user on form submit.
	 *
	 * @param int                          $userId
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param TranslatorInterface          $t
	 * @Route("/profile/edit/{userId}", name="edit_profile", defaults={"userId" = NULL}, requirements={"userId"="\d+"})
	 * @return RedirectResponse|Response
	 */
	public function editProfile($userId, Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $t)
	{
		// If the user is not logged in, redirect them.
		if (!$this->getUser())
		{
			$this->addFlash('error', $t->trans('user.edit.must_be_logged_in'));

			return $this->redirectToRoute('homepage');
		}

		$currentUser = $this->getUser();
		$user = $currentUser;

		// The user we're editing is not you.
		if ($userId && $userId != $currentUser->getId())
		{
			$entityManager = $this->getDoctrine()->getManager();
			$user = $entityManager->getRepository(User::class)->find($userId);

			// If the current user is not a moderator, redirect them.
			if (!$currentUser->hasRole('ROLE_MODERATOR'))
			{
				$this->addFlash('error', $t->trans('user.edit.only_administrators_can_edit_other_users'));

				return $this->redirectToRoute('edit_profile');
			}

			// If the user who is being edited is a moderator and the current user is not an administrator...
			elseif ($user->hasRole('ROLE_MODERATOR') && !$currentUser->hasRole('ROLE_ADMIN'))
			{
				$this->addFlash('error', $t->trans('user.edit.only_administrators_can_edit_other_administrators'));

				return $this->redirectToRoute('edit_profile');
			}
		}

		// Build the form.
		$form = $this->createForm(EditProfileType::class, NULL, [
			'currentUser' => $currentUser,
			'user' => $user,
		]);

		// Handle the form submission.
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid())
		{
			// Get the form data.
			$data = $form->getData();

			// Flags indicating whether or not the username, email, and password were updated.
			$usernameUpdated = FALSE;
			$emailUpdated = FALSE;
			$passwordUpdated = FALSE;
			$roleUpdated = FALSE;

			// Check for username / email duplicates.
			$usernameUnique = TRUE;
			$userByUsername = $this->getDoctrine()
								   ->getRepository(User::class)
								   ->findOneBy(['username' => $data['username']]);
			$emailUnique = TRUE;
			$userByEmail = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data['email']]);

			// If the username already exists in the database and does not belong to the current user.
			if ($userByUsername instanceof User && $userByUsername != $user)
			{
				$usernameUnique = FALSE;
				$this->addFlash('error', $t->trans('user.edit.username_taken'));
			}

			// If the email already exists in the database and does not belong to the current user.
			if ($userByEmail instanceof User && $userByEmail != $user)
			{
				$emailUnique = FALSE;
				$this->addFlash('error', $t->trans('user.edit.email_taken'));
			}

			// Update the username (if provided, and not a duplicate, and not the same as the current username).
			if ($usernameUnique && !empty($data['username']) && $userByUsername != $user)
			{
				$user->setUsername($data['username']);
				$this->addFlash('success', $t->trans('user.edit.username_updated'));
				$usernameUpdated = TRUE;
			}

			// Update the email (if provided, and not a duplicate, and not the same as the current one).
			if ($emailUnique && !empty($data['email']) && $userByEmail != $user)
			{
				$user->setEmail($data['email']);
				$this->addFlash('success', $t->trans('user.edit.email_updated'));
				$emailUpdated = TRUE;
			}

			// Update the user's password (if provided).
			$plainPassword = $data['plainPassword'];
			if (!empty($plainPassword))
			{
				$passwordEncoded = $passwordEncoder->encodePassword($user, $plainPassword);
				$user->setPassword($passwordEncoded);
				$this->addFlash('success', $t->trans('user.edit.password_updated'));
				$passwordUpdated = TRUE;
			}

			// Update the user's role (if provided).
			$role = $data['role'] ?? NULL;
			if (!empty($role))
			{
				if (!$currentUser->hasRole('ROLE_ADMIN') && ($role == 'ROLE_ADMIN' || $role == 'ROLE_MODERATOR'))
				{
					if ($role == 'ROLE_ADMIN')
					{
						$this->addFlash('error', $t->trans('user.edit.not_authorized_to_add_administrators'));
					}
					else
					{
						$this->addFlash('error', $t->trans('user.edit.not_authorized_to_add_moderators'));
					}
				}
				else
				{
					$user->setRole($role);
					$this->addFlash('success', $t->trans('user.edit.access_level.updated', ['username' => $user->getUsername()]));
					$roleUpdated = TRUE;
				}
			}

			// There were no errors, but no changes have been made either.
			if ($usernameUnique
				&& $emailUnique
				&& !$usernameUpdated
				&& !$emailUpdated
				&& !$passwordUpdated
				&& !$roleUpdated)
			{
				$this->addFlash('alert', $t->trans('user.edit.no_changes'));
			}

			// Update the database.
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();

			return $this->redirectToRoute('edit_profile', ['userId' => $user->getId()]);
		}

		return $this->render('user/edit_profile.html.twig', [
			'form' => $form->createView(),
			'user' => $user,
		]);
	}

	/**
	 * Logs the user in, or presents an error if the login did not succeed.
	 *
	 * @param AuthenticationUtils $authenticationUtils
	 * @param TranslatorInterface $t
	 * @Route("/login", name="login")
	 * @return Response
	 */
	public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $t)
	{
		// If the user is already logged in, redirect them.
		if ($this->getUser())
		{
			$username = $this->getUser()->getUsername();
			$this->addFlash('alert', $t->trans('user.login.already_logged_in', ['username' => $username]));
		}

		// Get the login error (if there is one).
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		// Display an error message.
		if ($error)
		{
			$this->addFlash('error', $t->trans('user.login.invalid_credentials'));
		}

		return $this->render('user/login.html.twig', ['lastUsername' => $lastUsername]);
	}

	/**
	 * Logs the user out.
	 *
	 * @param TranslatorInterface $t
	 * @Route("/logout", name="logout", methods={"GET"})
	 * @throws Exception
	 */
	public function logout(TranslatorInterface $t)
	{
		throw new Exception($t->trans('user.login.logout_success'));
	}

	/**
	 * Allows a new user to register an account. Also creates the new user on form submit.
	 *
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param TranslatorInterface          $t
	 * @Route("/register", name="register")
	 * @return RedirectResponse|Response
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $t)
	{
		// If the user is logged in, redirect them.
		if ($this->getUser())
		{
			$this->addFlash('error', $t->trans('user.register.already_logged_in'));

			return $this->redirectToRoute('homepage');
		}

		// 1) Build the form
		$user = new User();
		$form = $this->createForm(RegistrationFormType::class, $user);

		// 2) Handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid())
		{
			// 3) Encode the password (you could also do this via Doctrine listener)
			$passwordEncoded = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($passwordEncoded);

			// 4) Save the User!
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();

			return $this->redirectToRoute('homepage');
		}

		return $this->render('user/register.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * Displays a user's profile page, if applicable.
	 *
	 * @param int                          $userId
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param TranslatorInterface          $t
	 * @Route("/profile/view/{userId}", name="view_profile", requirements={"userId"="\d+"})
	 * @return RedirectResponse|Response
	 */
	public function view($userId, Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $t)
	{
		// Get the user.
		$user = $this->getDoctrine()->getRepository(User::class)->find($userId);

		// Throw an error if the user does not exist.
		if (!$user)
		{
			$this->addFlash('error', $t->trans('user.view.does_not_exist'));

			return $this->redirectToRoute('homepage');
		}

		// Render everything.
		return $this->render('user/view.html.twig', ['user' => $user]);
	}
}