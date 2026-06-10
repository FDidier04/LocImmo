<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Security\EventVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventController extends AbstractController
{
    // GET /api/events — liste publique (annonces publiés)
    #[Route('/api/events', name: 'api_events_list', methods: ['GET'])]
    #[Route('/api/properties', name: 'api_properties_list', methods: ['GET'])]
    public function list(EventRepository $repo, Request $request): JsonResponse
    {
        $events = $repo->findPublished([
            'city' => $request->query->get('city'),
            'propertyType' => $request->query->get('propertyType'),
            'offerType' => $request->query->get('offerType'),
            'maxRent' => $request->query->get('maxRent'),
        ]);
        return $this->json(array_map([$this, 'serializeEvent'], $events));
    }

    // GET /api/events/all — liste complète (admin/organizer)
    #[Route('/api/events/all', name: 'api_events_all', methods: ['GET'])]
    #[Route('/api/properties/all', name: 'api_properties_all', methods: ['GET'])]
    public function listAll(EventRepository $repo): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $events = $repo->findAll();
        } else {
            $events = $repo->findByOrganizer($user->getId());
        }

        return $this->json(array_map([$this, 'serializeEvent'], $events));
    }

    // GET /api/events/{id}
    #[Route('/api/events/{id}', name: 'api_events_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[Route('/api/properties/{id}', name: 'api_properties_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Event $event): JsonResponse
    {
        return $this->json($this->serializeEvent($event));
    }

    // POST /api/events
    #[Route('/api/events', name: 'api_events_create', methods: ['POST'])]
    #[Route('/api/properties', name: 'api_properties_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        if (!in_array('ROLE_ORGANIZER', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['message' => 'Forbidden: only agencies, owners and admins can create listings'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['message' => 'Invalid JSON'], 400);
        }

        $event = new Event();
        $event->setTitle($data['title'] ?? '');
        $event->setDescription($data['description'] ?? '');
        $event->setLocation($data['location'] ?? '');
        $event->setCity($data['city'] ?? '');
        $event->setDistrict($data['district'] ?? '');
        $event->setPropertyType($data['propertyType'] ?? '');
        $event->setOfferType($data['offerType'] ?? 'Location');
        $event->setMonthlyRent((int)($data['monthlyRent'] ?? 0));
        $event->setSurfaceM2((int)($data['surfaceM2'] ?? 0));
        $event->setBedrooms((int)($data['bedrooms'] ?? 0));
        $event->setBathrooms((int)($data['bathrooms'] ?? 0));
        $event->setImageUrl($data['imageUrl'] ?? null);
        $event->setMaxParticipants((int)($data['maxParticipants'] ?? 10));
        $event->setOrganizer($user);
        $event->setIsPublished($data['isPublished'] ?? false);

        try {
            $date = new \DateTime($data['eventDate'] ?? '+1 day');
            $event->setEventDate($date);
        } catch (\Exception) {
            return $this->json(['errors' => ['eventDate' => 'Invalid availability date format']], 422);
        }

        if (!empty($data['endDate'])) {
            try {
                $event->setEndDate(new \DateTime($data['endDate']));
            } catch (\Exception) {
                return $this->json(['errors' => ['endDate' => 'Invalid date format']], 422);
            }
        }

        if ($event->getEndDate() !== null && $event->getEndDate() <= $event->getEventDate()) {
            return $this->json(['errors' => ['endDate' => 'End date must be after event date']], 422);
        }

        $errors = $validator->validate($event);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 422);
        }

        $em->persist($event);
        $em->flush();

        return $this->json($this->serializeEvent($event), 201);
    }

    // PUT /api/events/{id}
    #[Route('/api/events/{id}', name: 'api_events_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    #[Route('/api/properties/{id}', name: 'api_properties_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        Event $event,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $this->denyAccessUnlessGranted(EventVoter::EDIT, $event);

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['message' => 'Invalid JSON'], 400);
        }

        if (isset($data['title']))           $event->setTitle($data['title']);
        if (isset($data['description']))     $event->setDescription($data['description']);
        if (isset($data['location']))        $event->setLocation($data['location']);
        if (isset($data['city']))            $event->setCity($data['city']);
        if (isset($data['district']))        $event->setDistrict($data['district']);
        if (isset($data['propertyType']))    $event->setPropertyType($data['propertyType']);
        if (isset($data['offerType']))       $event->setOfferType($data['offerType']);
        if (isset($data['monthlyRent']))     $event->setMonthlyRent((int)$data['monthlyRent']);
        if (isset($data['surfaceM2']))       $event->setSurfaceM2((int)$data['surfaceM2']);
        if (isset($data['bedrooms']))        $event->setBedrooms((int)$data['bedrooms']);
        if (isset($data['bathrooms']))       $event->setBathrooms((int)$data['bathrooms']);
        if (array_key_exists('imageUrl', $data)) $event->setImageUrl($data['imageUrl']);
        if (isset($data['maxParticipants'])) $event->setMaxParticipants((int)$data['maxParticipants']);
        if (isset($data['isPublished']))     $event->setIsPublished((bool)$data['isPublished']);
        if (isset($data['eventDate'])) {
            try {
                $event->setEventDate(new \DateTime($data['eventDate']));
            } catch (\Exception) {
                return $this->json(['errors' => ['eventDate' => 'Invalid date format']], 422);
            }
        }
        if (array_key_exists('endDate', $data)) {
            if ($data['endDate']) {
                try {
                    $event->setEndDate(new \DateTime($data['endDate']));
                } catch (\Exception) {
                    return $this->json(['errors' => ['endDate' => 'Invalid date format']], 422);
                }
            } else {
                $event->setEndDate(null);
            }
        }

        if ($event->getEndDate() !== null && $event->getEndDate() <= $event->getEventDate()) {
            return $this->json(['errors' => ['endDate' => 'End date must be after event date']], 422);
        }

        $errors = $validator->validate($event);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 422);
        }

        $em->flush();
        return $this->json($this->serializeEvent($event));
    }

    // DELETE /api/events/{id}
    #[Route('/api/events/{id}', name: 'api_events_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    #[Route('/api/properties/{id}', name: 'api_properties_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Event $event, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted(EventVoter::DELETE, $event);
        $em->remove($event);
        $em->flush();
        return $this->json(['message' => 'Listing deleted'], 200);
    }

    // PATCH /api/events/{id}/publish
    #[Route('/api/events/{id}/publish', name: 'api_events_publish', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    #[Route('/api/properties/{id}/publish', name: 'api_properties_publish', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    public function publish(Event $event, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted(EventVoter::PUBLISH, $event);
        $event->setIsPublished(!$event->isPublished());
        $em->flush();
        return $this->json($this->serializeEvent($event));
    }

    private function serializeEvent(Event $event): array
    {
        return [
            'id'                 => $event->getId(),
            'title'              => $event->getTitle(),
            'description'        => $event->getDescription(),
            'eventDate'          => $event->getEventDate()?->format('c'),
            'endDate'            => $event->getEndDate()?->format('c'),
            'location'           => $event->getLocation(),
            'city'               => $event->getCity(),
            'district'           => $event->getDistrict(),
            'propertyType'       => $event->getPropertyType(),
            'offerType'          => $event->getOfferType(),
            'monthlyRent'        => $event->getMonthlyRent(),
            'surfaceM2'          => $event->getSurfaceM2(),
            'bedrooms'           => $event->getBedrooms(),
            'bathrooms'          => $event->getBathrooms(),
            'imageUrl'           => $event->getImageUrl(),
            'maxParticipants'    => $event->getMaxParticipants(),
            'isPublished'        => $event->isPublished(),
            'createdAt'          => $event->getCreatedAt()?->format('c'),
            'participantsCount'  => $event->getConfirmedParticipantsCount(),
            'remainingPlaces'    => $event->getRemainingPlaces(),
            'isFull'             => $event->isFull(),
            'organizer'          => [
                'id'        => $event->getOrganizer()?->getId(),
                'firstName' => $event->getOrganizer()?->getFirstName(),
                'lastName'  => $event->getOrganizer()?->getLastName(),
            ],
        ];
    }
}
