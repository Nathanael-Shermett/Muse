<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostCategory;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
	/**
	 * @Route("/", name="homepage")
	 * @Route("{supercategory}/{category}", name="view_posts", requirements=
	 * {
	 *     "supercategory": "meta|philosophy|science|technology|innovation|strategy|politics|religion|society"
	 * })
	 */
	public function index(string $supercategory = NULL, string $category = NULL)
	{
		$posts = $this->getDoctrine()->getRepository(Post::class)->findByCategory($supercategory, $category);

		return $this->render('main/index.html.twig', ['posts' => $posts]);
	}
}
