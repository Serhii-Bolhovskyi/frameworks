<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/appointments')]
class AppointmentApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AppointmentRepository $appointmentRepository
    ) {
    }

    // GET /api/appointments - список усіх записів
    #[Route('', name: 'appointment_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $appointments = $this->appointmentRepository->findAll();

        $data = array_map(fn(Appointment $a) => $this->serialize($a), $appointments);

        return new JsonResponse($data);
    }

    // GET /api/appointments/{id} - один запис
    #[Route('/{id}', name: 'appointment_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $appointment = $this->appointmentRepository->find($id);

        if (!$appointment) {
            return new JsonResponse(['error' => 'Appointment not found'], 404);
        }

        return new JsonResponse($this->serialize($appointment));
    }

    // POST /api/appointments - створення нового запису
    #[Route('', name: 'appointment_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['clientName'], $data['serviceName'], $data['appointmentDate'], $data['status'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $appointment = new Appointment();
        $appointment->setClientName($data['clientName']);
        $appointment->setServiceName($data['serviceName']);
        $appointment->setAppointmentDate(new \DateTimeImmutable($data['appointmentDate']));
        $appointment->setStatus($data['status']);
        $appointment->setNotes($data['notes'] ?? null);

        $this->entityManager->persist($appointment);
        $this->entityManager->flush();

        return new JsonResponse($this->serialize($appointment), 201);
    }

    // PATCH /api/appointments/{id} - часткове оновлення
    #[Route('/{id}', name: 'appointment_update', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $appointment = $this->appointmentRepository->find($id);

        if (!$appointment) {
            return new JsonResponse(['error' => 'Appointment not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['clientName'])) {
            $appointment->setClientName($data['clientName']);
        }
        if (isset($data['serviceName'])) {
            $appointment->setServiceName($data['serviceName']);
        }
        if (isset($data['appointmentDate'])) {
            $appointment->setAppointmentDate(new \DateTimeImmutable($data['appointmentDate']));
        }
        if (isset($data['status'])) {
            $appointment->setStatus($data['status']);
        }
        if (isset($data['notes'])) {
            $appointment->setNotes($data['notes']);
        }

        $this->entityManager->flush();

        return new JsonResponse($this->serialize($appointment));
    }

    // DELETE /api/appointments/{id} - видалення запису
    #[Route('/{id}', name: 'appointment_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $appointment = $this->appointmentRepository->find($id);

        if (!$appointment) {
            return new JsonResponse(['error' => 'Appointment not found'], 404);
        }

        $this->entityManager->remove($appointment);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Appointment deleted'], 200);
    }

    private function serialize(Appointment $appointment): array
    {
        return [
            'id' => $appointment->getId(),
            'clientName' => $appointment->getClientName(),
            'serviceName' => $appointment->getServiceName(),
            'appointmentDate' => $appointment->getAppointmentDate()->format('Y-m-d H:i:s'),
            'status' => $appointment->getStatus(),
            'notes' => $appointment->getNotes(),
        ];
    }
}
