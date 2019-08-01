<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Entity\People;
use App\Entity\Rating;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\PeopleRepository;
use App\Repository\MovieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController
{
    /**
     * Current category browse by user
     *
     * @type App/Entity/Category
     */
    protected $current_category = null;

    /**
     * @Route("/", name="home")
     */
    public function home(MovieRepository $repo_movie, CategoryRepository $repo_category)
    {
        $categories = $repo_category->findWithMovies();

        $last_movies = $repo_movie->findLast(3);
        $best_movies = $repo_movie->findBest(3);
        $worst_movies = $repo_movie->findWorst(3);

        return $this->render('default/home.html.twig', [
            'categories' => $categories,
            'last_movies' => $last_movies,
            'best_movies' => $best_movies,
            'worst_movies' => $worst_movies,
        ]);
    }

    /**
     * @Route("/category/{slug}", name="category")
     *
     * @return void
     */
    public function category(Category $category)
    {
        $this->current_category = $category;
        return $this->render("default/category.html.twig", ["category" => $category]);
    }

    /**
     * @Route("/people/{slug}", name="people")
     *
     * @return void
     */
    public function people(People $people)
    {
        return $this->render("default/people.html.twig", ["people" => $people]);
    }

    public function navBar(CategoryRepository $repo_category, PeopleRepository $repo_people)
    {
        $categories = $repo_category->findBy([], ['title' => 'ASC']);
        $directors = $repo_people->findDirectors();

        return $this->render('default/_navbar.html.twig', [
            'categories' => $categories,
            'directors' => $directors,
            'current_category' => $this->current_category,
            'user' => $this->getUser()
        ]);
    }


    /**
     * @Route("/admin/rating/{id}", name="rating_delete")
     *
     * @return void
     */
    public function rating_delete(Rating $rating, ObjectManager $manager)
    {
        $movie = $rating->getMovie();

        $manager->remove($rating);
        $manager->flush();

        return $this->redirectToRoute('movie_show', ['slug' => $movie->getSlug()]);
    }


    /**
     * @Route("/rating_like/{id}", name="rating_like")
     * @IsGranted({"ROLE_USER"})
     *
     * @return void
     */
    public function rating_like(Rating $rating, ObjectManager $manager)
    {
        $user_id = $this->getUser()->getId();

        $rating_likes = $rating->getLikes();
        if (in_array($user_id, $rating_likes)) {
            if (($key = array_search($user_id, $rating_likes)) !== false) {
                unset($rating_likes[$key]);
            }
        } else {
            $rating_likes[] = $user_id;
        }
        $rating->setLikes($rating_likes);

        $rating_dislikes = $rating->getDislikes();
        if (in_array($user_id, $rating_dislikes)) {
            if (($key = array_search($user_id, $rating_dislikes)) !== false) {
                unset($rating_dislikes[$key]);
                $rating->setDislikes($rating_dislikes);
            }
        }

        $manager->flush();
        return $this->json(['likes' => count($rating_likes), 'dislikes' => count($rating_dislikes)]);
    }



    /**
     * @Route("/rating_dislike/{id}", name="rating_dislike")
     * @IsGranted({"ROLE_USER"})
     *
     * @return void
     */
    public function rating_dislike(Rating $rating, ObjectManager $manager)
    {
        $user_id = $this->getUser()->getId();

        $rating_dislikes = $rating->getDislikes();
        if (in_array($user_id, $rating_dislikes)) {
            if (($key = array_search($user_id, $rating_dislikes)) !== false) {
                unset($rating_dislikes[$key]);
            }
        } else {
            $rating_dislikes[] = $user_id;
        }

        $rating->setDislikes($rating_dislikes);

        $rating_likes = $rating->getLikes();
        if (in_array($user_id, $rating_likes)) {
            if (($key = array_search($user_id, $rating_likes)) !== false) {
                unset($rating_likes[$key]);
                $rating->setLikes($rating_likes);
            }
        }

        $manager->flush();
        return $this->json(['likes' => count($rating_likes), 'dislikes' => count($rating_dislikes)]);
    }
}
