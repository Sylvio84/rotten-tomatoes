<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Rating;
use App\Form\RatingType;

/**
 * #@Route("/movie")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/movies", name="movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/movie_search", name="movie_search")
     */
    public function search(MovieRepository $movieRepository, Request $request)
    {
        $query = $request->get('query');
        return $this->render('movie/search.html.twig', [
            'query' => $query,
            'movies' => $movieRepository->search($query),
        ]);
    }

    /**
     * @Route("/admin/movie/new", name="movie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($movie);
            $entityManager->flush();

            return $this->redirectToRoute('movie_show', ['slug' => $movie->getSlug()]);
        }

        return $this->render('movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/movie/{slug}", name="movie_show")
     */
    public function show(Movie $movie, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        if ($user) {
            $rating = new Rating();
            $rating->setMovie($movie);
            $rating->setAuthor($user);

            $form = $this->createForm(RatingType::class, $rating);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($rating);
                $manager->flush();

                return $this->redirectToRoute('movie_show', ['slug' => $movie->getSlug()]);
            }
        }

        return $this->render("movie/movie.html.twig", [
            "movie" => $movie,
            'ratingForm' => isset($form) ? $form->createView() : false,
            'user' => $this->getUser(),
            'alreadyHasRating' => $user && $user->hasRate($movie)
        ]);
    }

    /**
     * @Route("/admin/movie/{slug}/edit", name="movie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Movie $movie): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('movie_show', ['slug' => $movie->getSlug()]);
        }

        return $this->render('movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/movie/{slug}", name="movie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Movie $movie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $movie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($movie->getRatings() as $rating) {
                $entityManager->remove($rating);
            }
            $entityManager->remove($movie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('movie_index');
    }
}
