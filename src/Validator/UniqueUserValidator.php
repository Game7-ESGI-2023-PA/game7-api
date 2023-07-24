<?php

namespace App\Validator;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserValidator extends ConstraintValidator
{

    public function __construct(private readonly DocumentManager $documentManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        $editingDocument = $this->context->getObject();
        $repository = $this->documentManager->getRepository(User::class);
        $isValid = true;
        $message = null;

        $existingUserWithSameNickname = $repository->findOneBy(['nickname' => $value]);
        $existingUserWithSameEmail= $repository->findOneBy(['email' => $value]);
        if (
            (null !== $existingUserWithSameNickname && $existingUserWithSameNickname !== $editingDocument) ||
            (null !== $existingUserWithSameEmail && $existingUserWithSameEmail !== $editingDocument)
        ) {
            $isValid = false;
            $message = "{{ value }} est déjà pris";
        }

        if (!$isValid) {
            $this->context->buildViolation($message)
                ->setParameter('{{ value }}', $value)
                ->setCode(UniqueUser::INVALID_STATUS_ERROR)
                ->addViolation();
        }
    }
}
