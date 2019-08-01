<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\People;
use App\Entity\Movie;
use App\Entity\Rating;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppFixtures extends Fixture
{

    /**
     * @type UserPasswordEncoderInterface;
     */
    protected $encoder;

    /**
     * @type ContainerInterface;
     */
    protected $container;

    public function __construct(UserPasswordEncoderInterface $encoder, ContainerInterface $container)
    {
        $this->encoder = $encoder;
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = $this->container->get('faker')->getFaker();

        $categories_title = ["Comédie", "Drame", "Policier", "Aventure", "Romance", "Science-Fiction", "Fantastique", "Animation", "Action", "Documentaire"];
        $categories = [];
        for ($c = 0; $c < 10; $c++) {
            $category = new Category();
            $category->setTitle($categories_title[$c]);
            $manager->persist($category);
            $categories[] = $category;
        }

        $peoples = [];
        for ($p = 0; $p < 20; $p++) {
            $people = new People();
            $people->setLastName($faker->lastName)
                ->setFirstName($faker->firstNameMale)
                ->setDescription($faker->markdown())
                ->setBirthday($faker->dateTimeBetween('-100 years'), '-10 years')
                ->setPicture('media/images/male/' . ($p + 1) . '.jpg');
            $manager->persist($people);
            $peoples[] = $people;
        }
        for ($p = 0; $p < 20; $p++) {
            $people = new People();
            $people->setLastName($faker->lastName)
                ->setFirstName($faker->firstNameFemale)
                ->setDescription($faker->markdown())
                ->setBirthday($faker->dateTimeBetween('-100 years'), '-10 years')
                ->setPicture('media/images/female/' . ($p + 1) . '.jpg');
            $manager->persist($people);
            $peoples[] = $people;
        }

        $users = [];
        for ($u = 0; $u < 20; $u++) {
            $user = new User();
            $user->setEmail($u === 0 ? 'user1@gmail.com' : $faker->email)
                ->setName($faker->firstNameMale)
                ->setAvatar("https://randomuser.me/api/portraits/men/" . $u . ".jpg");
            $password = $this->encoder->encodePassword($user, "pass");
            $user->setPassword($password);
            $manager->persist($user);
            $users[] = $user;
        }
        for ($u = 0; $u < 20; $u++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setName($faker->firstNameFemale)
                ->setAvatar("https://randomuser.me/api/portraits/women/" . $u . ".jpg");
            $password = $this->encoder->encodePassword($user, "pass");
            $user->setPassword($password);
            $manager->persist($user);
            $users[] = $user;
        }
        $user = new User();
        $user->setEmail("sylvain@zore.org")
            ->setName("Sylvio")
            ->setAvatar("https://avatars3.githubusercontent.com/u/33416490?s=460&v=4");
        $password = $this->encoder->encodePassword($user, "pass");
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        $user = new User();
        $user->setEmail("modo@gmail.com")
            ->setName("SuperModo")
            ->setAvatar("https://vignette.wikia.nocookie.net/club-penguin-rewritten/images/8/86/Modpin.png/revision/latest?cb=20190424044053");
        $password = $this->encoder->encodePassword($user, "pass");
        $user->setPassword($password);
        $user->setRoles(['ROLE_MODERATOR']);
        $manager->persist($user);

        $manager->flush();

        $movies = [];
        $no_images = [12, 28, 40, 74, 84, 96];
        for ($m = 1; $m <= 100; $m++) {
            if (!in_array($m, $no_images)) {
                $movie = new Movie();
                $movie->setTitle($faker->realText(30))
                    ->setSynopsys($faker->markdown())
                    ->setReleasedAt($faker->dateTimeBetween('-40 years'))
                    ->setPoster("http://www.hidecut.com/wp-content/uploads/2015/05/Pixel-faker-mashup-movie-" . $m . ".jpg")
                    ->setDirector($faker->randomElement($peoples));

                $actors = $faker->randomElements($peoples, $faker->numberBetween(4, 8));
                foreach ($actors as $actor) {
                    $movie->addActor($actor);
                }
                $movie_categories = $faker->randomElements($categories, $faker->numberBetween(1, 3));
                foreach ($movie_categories as $category) {
                    $movie->addCategory($category);
                }

                for ($r = 0; $r < $faker->numberBetween(0, 50); $r++) {
                    $rating = new Rating();
                    $rating->setAuthor($faker->randomElement($users))
                        ->setMovie($movie)
                        ->setNotation($faker->numberBetween(1, 5));
                    if ($faker->numberBetween(1, 10) > 1) {
                        $rating->setComment($faker->text);
                    }

                    $like_users = $faker->randomElements($users, $faker->numberBetween(0, 20));
                    $likes = $dislikes = [];
                    $i_like = mt_rand(0, count($like_users));
                    foreach ($like_users as $i => $user) {
                        if ($i <= $i_like) {
                            $likes[] = $user->getId();
                        } else {
                            $dislikes[] = $user->getId();
                        }
                    }
                    $rating->setLikes($likes);
                    $rating->setDislikes($dislikes);

                    $manager->persist($rating);
                }

                $manager->persist($movie);
                $movies[] = $movie;
            }
        }

        $manager->flush();
    }
}
