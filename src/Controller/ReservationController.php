<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Entity\OpeningHours;
use App\Repository\ReservationRepository;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\Parser\DateTimeParseException;
use Brick\DateTime\TimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Brick\DateTime\LocalTime;
use App\Repository\OpeningHoursRepository;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $reservation = new Reservation();
        if ($user != null) {
            $reservation->setLastName($user->getLastName());
            $reservation->setNbPlaces($user->getDefaultNbPlaces());
            $reservation->setAllergy($user->getDefaultAllergy());
        }
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour réserver.');
            }
            $entityManager->persist($reservation);
            $entityManager->flush();
            $this->addFlash("success", "Voici le numéro de votre réservation: " . $reservation->getId());
        }

        return $this->render('reservation/index.html.twig', [
            'reservationForm' => $form->createView(),
            'lastName' => $user,
        ]);
    }

    #[Route('/a/reservation_slots', name: 'ajax_get_reservation_slots', methods: ['POST'])]
    public function getReservationSlots(
        Request $request,
        OpeningHoursRepository $ohRepo,
        ReservationRepository $reservRepo
    ): JsonResponse {
        $datenow = LocalDateTime::now(TimeZone::parse('Europe/Paris'));
        $date60d = $datenow->plusDays(60);

        try {
            $booking = LocalDateTime::parse(json_decode($request->getContent(), true)["bookingDate"]);
        } catch (DateTimeParseException $e) {
            return new JsonResponse(["slots" => "-"]);
        }

        $openHours = $ohRepo->findAll();
        $oh = array_filter($openHours, function ($val) use ($booking) {
            return $val->getDay()->name === $booking->getDayOfWeek()->__toString();
        });
        $oh = reset($oh);

        if ($oh === false) {
            return new JsonResponse(["slots" => "Aucun horaire configuré pour ce jour"]);
        }

        if ($booking < $datenow || $booking > $date60d) {
            $maxPlaces = 0;
        } elseif ($oh->isDayClosed()) {
            return new JsonResponse(["slots" => 'Le restaurant est fermé le ' . $oh->getDay()->value]);
        } else {
            $maxPlaces = $this->getMaxPlacesForBooking($booking, $oh);
            if ($maxPlaces === null) {
                return new JsonResponse(["slots" => 'Vous ne pouvez pas réserver pendant la dernière heure d\'un service']);
            }
            if ($maxPlaces === false) {
                return new JsonResponse(["slots" => 'Merci de réserver pendant les horaires d\'ouverture']);
            }
        }

        $reservations = $reservRepo->getByDateAndService($booking->toNativeDateTime(), $oh);
        $rSlots = 0;
        foreach ($reservations as $r) {
            $rSlots += $r->getNbPlaces();
        }

        $slots = max(0, $maxPlaces - $rSlots);

        return new JsonResponse(["slots" => strval($slots), "maxPlaces" => strval($maxPlaces), "rSlots" => $rSlots]);
    }

    private function getMaxPlacesForBooking(LocalDateTime $booking, OpeningHours $oh): int|null|false
    {
        $hasLunch = !$oh->isLunchClosed() && $oh->getLunchStart() && $oh->getLunchend();
        $hasEvening = !$oh->isEveningClosed() && $oh->getEveningStart() && $oh->getEveningEnd();

        $services = [];
        if ($hasLunch) {
            $services[] = [
                'start' => new LocalDateTime($booking->getDate(), LocalTime::fromNativeDateTime($oh->getLunchStart())),
                'end' => new LocalDateTime($booking->getDate(), LocalTime::fromNativeDateTime($oh->getLunchend())),
                'maxPlaces' => $oh->getLunchMaxPlaces(),
            ];
        }
        if ($hasEvening) {
            $services[] = [
                'start' => new LocalDateTime($booking->getDate(), LocalTime::fromNativeDateTime($oh->getEveningStart())),
                'end' => new LocalDateTime($booking->getDate(), LocalTime::fromNativeDateTime($oh->getEveningEnd())),
                'maxPlaces' => $oh->getEveningMaxPlaces(),
            ];
        }

        foreach ($services as $service) {
            if ($booking->isAfterOrEqualTo($service['start']) && $booking->isBefore($service['end'])) {
                if ($booking->isAfterOrEqualTo($service['end']->minusMinutes(60))) {
                    return null; // last hour
                }
                return $service['maxPlaces'];
            }
        }

        return false; // outside hours
    }
}