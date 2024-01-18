<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor as DoctrineRemoveProcessor;
use ApiPlatform\Validator\ValidatorInterface;

readonly class DeleteManufacturerProcessor implements ProcessorInterface
{

    public function __construct(
        private DoctrineRemoveProcessor $doctrineProcessor,
        private ValidatorInterface $validator,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->validator->validate($data, ['groups' => ['deleteValidation']]);

        /*if ($data->getProducts()->count()) {
            return new JsonResponse([
                'message' => 'The manufacturer has products associated, cannot be deleted.'
            ], Response::HTTP_CONFLICT);
        }*/

        $this->doctrineProcessor->process($data, $operation, $uriVariables, $context);
    }
}
