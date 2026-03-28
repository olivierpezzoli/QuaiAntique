<?php

namespace App\Validator;


use Brick\DateTime\LocalTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\TimeZone;

class ReservationDateValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint): void
    {
        If (!$constraint instanceof ReservationDate) {
            throw new UnexpectedTypeException($constraint, ReservationDate::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof \DateTime) {
            throw new UnexpectedValueException($value, 'DateTime');
        }

        $datenow = LocalDateTime::now(TimeZone::parse('Europe/Paris'));
        $date60d = $datenow->plusDays(60);
        $booking = LocalDateTime::fromNativeDateTime($value);
        $bookingDay = $booking->getDayOfWeek()->__toString();

        $openingHours = array_filter($constraint->openHours, function($val) use ($bookingDay) {
           return $val->getDay()->name === $bookingDay;
        });
        $openingHours = reset($openingHours);

        if ($openingHours === false) {
            $this->context->addViolation($constraint->messageDayClosed);
            return;
        }

        $time = $booking->getTime();

        if ($booking < $datenow) {
            $this->context->addViolation($constraint->messageBefore);
            return;
        }

        if ($booking > $date60d) {
            $this->context->addViolation($constraint->messageAfter);
            return;
        }

        if ($openingHours->isDayClosed()) {
            $this->context->addViolation($constraint->messageDayClosed);
            return;
        }

        if ($openingHours->isLunchClosed()) {
            $evening_start = LocalTime::fromNativeDateTime($openingHours->getEveningStart());
            $evening_end = LocalTime::fromNativeDateTime($openingHours->getEveningEnd());
            if ($time->isBefore($evening_start) || $time->isAfter($evening_end)) {
                $this->context->addViolation($constraint->messageOutsideOh);
            } elseif ($time->isAfter($evening_end->minusMinutes(60)) && $time->isBeforeOrEqualTo($evening_end)) {
                $this->context->addViolation($constraint->messageLastHour);
            }
        } elseif ($openingHours->isEveningClosed()) {
            $lunch_start = LocalTime::fromNativeDateTime($openingHours->getLunchStart());
            $lunch_end = LocalTime::fromNativeDateTime($openingHours->getLunchend());
            if ($time->isBefore($lunch_start) || $time->isAfter($lunch_end)) {
                $this->context->addViolation($constraint->messageOutsideOh);
            } elseif ($time->isAfter($lunch_end->minusMinutes(60)) && $time->isBeforeOrEqualTo($lunch_end)) {
                $this->context->addViolation($constraint->messageLastHour);
            }
        } else {
            $lunch_start = LocalTime::fromNativeDateTime($openingHours->getLunchStart());
            $lunch_end = LocalTime::fromNativeDateTime($openingHours->getLunchend());
            $evening_start = LocalTime::fromNativeDateTime($openingHours->getEveningStart());
            $evening_end = LocalTime::fromNativeDateTime($openingHours->getEveningEnd());
            if ($time->isBefore($lunch_start) || $time->isAfter($evening_end) || ($time->isAfter($lunch_end) && $time->isBefore($evening_start))) {
                $this->context->addViolation($constraint->messageOutsideOh);
            } elseif (($time->isAfter($lunch_end->minusMinutes(60)) && $time->isBeforeOrEqualTo($lunch_end)) || ($time->isAfter($evening_end->minusMinutes(60)) && $time->isBeforeOrEqualTo($evening_end))) {
                $this->context->addViolation($constraint->messageLastHour);
            }
        }
    }
}