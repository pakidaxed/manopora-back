<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User\User;
use App\Entity\User\UserPicture;
use App\Repository\User\UserPictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Validation\PayloadValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class UserPictureController extends AbstractController
{
    private const PICTURES_LIMIT = 10;

    public function __construct(
        private readonly UserPictureRepository    $userPictureRepository,
        private readonly EntityManagerInterface   $entityManager,
        private readonly RequestStack             $requestStack,
        private readonly PayloadValidationService $payloadValidation,
        private                                   $pictureDirectory,
    )
    {
    }

    #[Route('/user/pictures', name: 'user_pictures_get', methods: 'GET')]
    public function getUserPictures(): JsonResponse
    {
        if (!$this->getUserPicturesFromDb($this->getUser())) {
            return $this->json([
                'mainPicturePath' => $this->userPictureRepository->findMainImagePath($this->getUser()) ?? null,
                'message' => 'No pictures'
            ], 400);
        }

        return $this->json([
            'mainPicturePath' => $this->userPictureRepository->findMainImagePath($this->getUser()) ?? null,
            'pictures' => $this->getUserPicturesFromDb($this->getUser())
        ]);
    }

    #[Route('/user/pictures', name: 'user_pictures_post', methods: 'POST')]
    public function updateUserPictures(): JsonResponse
    {
        $data = $this->requestStack->getCurrentRequest()->files->get('uploadedPicture');
        if (!$data instanceof UploadedFile) {
            return $this->json([
                'errors' => [['field' => 'file', 'message' => 'Pridėkite nuotrauką']]
            ], 400);
        }

        $errors = $this->payloadValidation->validatePayload($data, [
            new File([
                'maxSize' => '2M',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ],
            ])
        ], 'file');

        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }

        $filename = md5($this->getUser()->getId() . $data->getFilename() . mt_rand(1, 9999)) . '.' . $data->guessExtension();
        $data->move($this->pictureDirectory, $filename);

        $userPicture = new UserPicture();
        $userPicture->setOwner($this->getUser());

        if (!$this->getUserPicturesFromDb($this->getUser())) {
            $userPicture->setMain(true);
        }

        $userPicture->setPath($filename);
        $this->entityManager->persist($userPicture);
        $this->entityManager->flush();

        return $this->json(null, 201);
    }

    #[Route('/user/pictures', name: 'user_picture_patch', methods: 'PATCH')]
    public function setUserPictureAsMain(): JsonResponse
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        if (!$data) return $this->json(['field' => 'file', 'errors' => [['message' => 'Invalid data']]], 400);

        $userPicture = $this->getSinglePictureFromDb($data->id);

        if (!$userPicture) {
            return $this->json([
                'errors' => [['field' => 'file', 'message' => 'No such picture']]
            ], 400);
        }

        $currentMainPicture = $this->userPictureRepository->findOneByMain(true) ?? null;
        if ($currentMainPicture) {
            $currentMainPicture->setMain(false);
            $this->entityManager->persist($currentMainPicture);
        }

        $userPicture->setMain(true);

        $this->entityManager->persist($userPicture);
        $this->entityManager->flush();

        return $this->json(null, 200);
    }

    #[Route('/user/pictures', name: 'user_picture_delete', methods: 'DELETE')]
    public function deleteUserPicture(): JsonResponse
    {
        $data = json_decode($this->requestStack->getCurrentRequest()->getContent());

        if (!$data) return $this->json(['field' => 'file', 'errors' => [['message' => 'Invalid data']]], 400);

        $userPicture = $this->getSinglePictureFromDb($data->id);

        if (!$userPicture) {
            return $this->json([
                'errors' => [['field' => 'file', 'message' => 'No such picture']]
            ], 400);
        }

        $filePath = $this->pictureDirectory . '/' . $userPicture->getPath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->userPictureRepository->remove($userPicture);
        $this->entityManager->flush();

        return $this->json(null, 200);
    }

    private function getUserPicturesFromDb(User $user): ?array
    {
        return $this->userPictureRepository->getAllPicturesByOwner($user) ?? null;
    }

    private function getSinglePictureFromDb(int $id): ?UserPicture
    {
        return $this->userPictureRepository->findOneById($id) ?? null;
    }

    //TODO reikia padaryt limitationa kad nebeleistu uploadint
    //TODO refactorint kaip data paimam
}