<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Helper\DownloadTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixture
{
    use DownloadTrait;

    private $passwordEncoder;
    private $avatarsDirectory;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, $avatarsDirectory)
    {
        $this->avatarsDirectory = $avatarsDirectory;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(
            3,
            'main_users',
            function ($i) use ($manager) {
                $user = new User();
                $user->setEmail(sprintf('spacebar%d@example.com', $i));

                $url = 'https://robohash.org/'.sprintf('spacebar%d', $i);

                $imagename = basename($url);
                $image = DownloadTrait::getimg($url);
                file_put_contents($this->avatarsDirectory.$imagename.'.png', $image);

                $user->setImageFilename($imagename);

                $user->setFirstName($this->faker->firstName);
                $user->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user,
                        'engage'
                    )
                );
                if ($this->faker->boolean) {
                    $user->setTwitterUsername($this->faker->userName);
                }
                $user->agreeToTerms();

                $apiToken1 = new ApiToken($user);
                $apiToken2 = new ApiToken($user);
                $manager->persist($apiToken1);
                $manager->persist($apiToken2);

                return $user;
            }
        );

        $this->createMany(
            3,
            'admin_users',
            function ($i) {
                $user = new User();
                $user->setEmail(sprintf('admin%d@thespacebar.com', $i));

                $url = 'https://robohash.org/'.sprintf('spacebar%d', $i);

                $imagename = basename($url);
                $image = UserFixture::getimg($url);
                file_put_contents($this->avatarsDirectory.$imagename.'.png', $image);

                $user->setImageFilename($imagename);

                $user->setFirstName($this->faker->firstName);
                $user->setRoles(['ROLE_ADMIN']);
                $user->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $user,
                        'engage'
                    )
                );
                $user->agreeToTerms();

                return $user;
            }
        );

        $manager->flush();
    }
}
