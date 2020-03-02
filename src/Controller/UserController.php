<?php

// src/Controller/UserController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\EditProfileType;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
	/**
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @Route("/profile/edit/{user_id}", name="edit_profile", defaults={"user_id" = NULL}, requirements={"user_id"="\d+"})
	 * @return
	 */
	public function editProfile($user_id, Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		// Ensure the user logged in.
		$this->denyAccessUnlessGranted('ROLE_USER', NULL, 'You must be logged in to access this page.');

		$currentUser = $this->getUser();
		$user = $currentUser;

		// The user we're editing is not you.
		if ($user_id)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$user = $entityManager->getRepository(User::class)->find($user_id);
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
				$this->addFlash('error', 'The username you provided belongs to another user and has not been updated.');
			}

			// If the email already exists in the database and does not belong to the current user.
			if ($userByEmail instanceof User && $userByEmail != $user)
			{
				$emailUnique = FALSE;
				$this->addFlash('error', 'The email you provided belongs to another user and has not been updated.');
			}

			// Update the username (if provided, and not a duplicate, and not the same as the current username).
			if ($usernameUnique && !empty($data['username']) && $userByUsername != $user)
			{
				$user->setUsername($data['username']);
				$this->addFlash('success', 'Your username has been updated.');
				$usernameUpdated = TRUE;
			}

			// Update the email (if provided, and not a duplicate, and not the same as the current one).
			if ($emailUnique && !empty($data['email']) && $userByEmail != $user)
			{
				$user->setEmail($data['email']);
				$this->addFlash('success', 'Your email has been updated.');
				$emailUpdated = TRUE;
			}

			// Update the user's password (if provided).
			$plainPassword = $data['plainPassword'];
			if (!empty($plainPassword))
			{
				$passwordEncoded = $passwordEncoder->encodePassword($user, $plainPassword);
				$user->setPassword($passwordEncoded);
				$this->addFlash('success', 'Your password has been updated.');
				$passwordUpdated = TRUE;
			}

			// Update the user's role (if provided).
			$role = $data['role'] ?? NULL;
			if (!empty($role))
			{
				if (!$currentUser->hasRole('ROLE_ADMIN'))
				{
					$this->addFlash('error', "You are not authorized to modify administrator nor moderator access levels.");
				}
				else
				{
					$user->setRole($role);
					$this->addFlash('success', "{$user->getUsername()}'s access level has been updated.");
					$roleUpdated = TRUE;
				}
			}

			// There were no errors, but no changes have been made either.
			if ($usernameUnique &&
				$emailUnique &&
				!$usernameUpdated &&
				!$emailUpdated &&
				!$passwordUpdated &&
				!$roleUpdated)
			{
				$this->addFlash('alert', 'You did not provide any new data. Therefore, no changes have been made.');
			}

			// Update the database.
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();

			return $this->redirectToRoute('edit_profile', ['user_id' => $user->getId()]);
		}

		return $this->render('user/edit_profile.html.twig', [
			'form' => $form->createView(),
			'user' => $user,
		]);
	}

	/**
	 * @Route("/login", name="login")
	 * @param AuthenticationUtils $authenticationUtils
	 * @return string
	 */
	public function login(AuthenticationUtils $authenticationUtils)
	{
		// If the user is already logged in, redirect them.
		if ($this->getUser())
		{
			$username = $this->getUser()->getUsername();
			$this->addFlash('alert', "Just a heads up—you are currently logged in as $username.
			If you are trying to switch accounts and log in as someone else, you can still do so below.
			Otherwise, no further action is necessary.");
		}

		// Get the login error (if there is one).
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		// Display an error message.
		if ($error)
		{
			$this->addFlash('error', 'The username and password you entered did not match any existing accounts.');
		}

		return $this->render('user/login.html.twig', ['last_username' => $lastUsername]);
	}

	/**
	 * @Route("/logout", name="logout", methods={"GET"})
	 */
	public function logout()
	{
		throw new \Exception('You have been successfully logged out.');
	}

	/**
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @Route("/register", name="register")
	 * @return
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		// If the user is logged in, redirect them.
		if ($this->getUser())
		{
			$this->addFlash('error', 'You are currently logged in. In order to make a new account, please log out first.');

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
	 * @param Request $request
	 * @Route("/profile/view/{user_id}", name="view_profile", requirements={"user_id"="\d+"})
	 * @return
	 */
	public function view($user_id, Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		// Get the user.
		$user = $this->getDoctrine()->getRepository(User::class)->find($user_id);

		// Throw an error if the user does not exist.
		if (!$user)
		{
			throw $this->createNotFoundException("No user found for ID #$user_id.");
		}

		// Render everything.
		return $this->render('user/view.html.twig', [
			'user' => $user,
		]);
	}
}