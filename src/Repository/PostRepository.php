<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method Post|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class PostRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Post::class);
	}

	public function findByCategory($supercategory = NULL, $category = NULL)
	{
		if ($supercategory == NULL && $category == NULL)
		{
			return $this->createQueryBuilder('p')->where('p.deleted = 0')->getQuery()->getResult();
		}
		elseif ($supercategory == NULL)
		{
			return $this->createQueryBuilder('p')
						->innerJoin('p.categories', 'c')
						->andWhere('c.name = :category')
						->andWhere('p.deleted = 0')
						->setParameter('category', $category)
						->getQuery()
						->getResult();
		}
		elseif ($category == NULL)
		{
			return $this->createQueryBuilder('p')
						->innerJoin('p.categories', 'c')
						->andWhere('c.supercategory = :supercategory')
						->andWhere('p.deleted = 0')
						->setParameter('supercategory', $supercategory)
						->getQuery()
						->getResult();
		}
		else
		{
			return $this->createQueryBuilder('p')
						->innerJoin('p.categories', 'c')
						->andWhere('c.supercategory = :supercategory')
						->andWhere('p.deleted = 0')
						->setParameter('supercategory', $supercategory)
						->andWhere('c.name = :category')
						->setParameter('category', $category)
						->getQuery()
						->getResult();
		}
	}
}
