<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostCategory;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
	/**
	 * Index page.
	 *
	 * Displays posts. Allows for two optional URL parameters to filter posts by supercategory and/or category.
	 *
	 * @param string $supercategory
	 * @param string $category
	 * @Route("/", name="homepage")
	 * @Route("{supercategory}/{category}", name="view_posts", requirements=
	 * {
	 *     "supercategory": "meta|philosophy|science|technology|innovation|strategy|politics|religion|society"
	 * })
	 * @return Response
	 */
	public function index($supercategory = '', $category = '')
	{
		$posts = $this->getDoctrine()->getRepository(Post::class)->findByCategory($supercategory, $category);

		return $this->render('main/index.html.twig', [
			'posts' => $posts,
			'supercategory' => $supercategory,
			'category' => $category,
		]);
	}
}