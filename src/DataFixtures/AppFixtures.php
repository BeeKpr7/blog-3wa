<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Tag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;
use Bluemmb;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private SluggerInterface $slugger)
    {
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        $faker->addProvider(new Bluemmb\Faker\PicsumPhotosProvider($faker));
        $tags=[];
        $users=[];

        for ($i = 0; $i < 15; $i++) {
            $tag= new Tag();
            $tag->setName($faker->word);
            $manager->persist($tag);
            $tags[]=$tag;
        }


        //new Users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setName($faker->name);
            $user->setPassword($this->hasher->hashPassword($user,'password'));
            $manager->persist($user);
            $users[]=$user;
        }

        //new Posts
        for ($i = 0; $i < 100; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence);
            $post->setContent($faker->paragraph);
            $post->setCreatedAt($faker->dateTime);
            $post->setUpdatedAt($faker->dateTime);
            $post->setSlug($this->slugger->slug($post->getTitle()));
            $post->setImage($faker->imageUrl(600,400,true));
            $post->setAuthor($users[rand(0,9)]);
            for ($j = 0; $j < rand(1,5); $j++) {
                $post->addTag($tags[rand(0,14)]);
            }
            $manager->persist($post);
        }

        $manager->flush();
    }
}
