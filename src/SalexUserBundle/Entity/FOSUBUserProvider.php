<?php

// Change the namespace according to your project.
namespace SalexUserBundle\Entity;

use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Handler\UploadHandler;


class FOSUBUserProvider extends BaseClass {

    private $kernelRootDir;
    private $uploadHandler;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager fOSUB user provider
     * @param array $properties  property mapping
     */
    public function __construct(UserManagerInterface $userManager, array $properties, string $kernelRootDir, UploadHandler $uploadHandler)
    {
        $this->kernelRootDir = $kernelRootDir;
        $this->uploadHandler = $uploadHandler;
        parent::__construct($userManager, $properties);
    }


    public function connect(UserInterface $user, UserResponseInterface $response) {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        // On connect, retrieve the access token and the user id
        $service = $response->getResourceOwner()->getName();

        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';

        // Disconnect previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        // Connect using the current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
        $username = $response->getUsername();
        $email = $response->getEmail() ? $response->getEmail() : $username;
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        // If the user is new
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set' . ucfirst($service);
            $setter_id = $setter . 'Id';
            $setter_token = $setter . 'AccessToken';
            $setter_first_name = 'setFirstName';
            $setter_last_name = 'setLastName';

            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());

            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($this->generateRandomUsername($username, $response->getResourceOwner()->getName()));
            $user->setEmail($email);
            $user->setPassword($username);
            $user->setEnabled(true);
            $user->setRoles(['USER_ROLE']);

            $user->$setter_first_name($response->getFirstName());
            $user->$setter_last_name($response->getLastName());

            $new_file = $this->kernelRootDir.'/../web/images/profile/'.$username.'.jpg';
            copy($response->getProfilePicture(), $new_file);
            $mimeType = mime_content_type($new_file);
            $size = filesize ($new_file);
            $finalName = md5(uniqid(rand(), true)).".jpg";
            $uploadedFile = new UploadedFile($new_file, $finalName, $mimeType, $size, null, true);
            $user->setImageFile($uploadedFile);

            $this->uploadHandler->upload($user,'imageFile');

            $this->userManager->updateUser($user);
            return $user;
        }

        // If the user exists, use the HWIOAuth
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        // Update the access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

    /**
     * Generates a random username with the given
     * e.g 12345_github, 12345_facebook
     *
     * @param string $username
     * @param type $serviceName
     * @return type
     */
    private function generateRandomUsername($username, $serviceName){
        if(!$username){
            $username = "user". uniqid((rand()), true) . $serviceName;
        }

        return $username. "_" . $serviceName;
    }
}