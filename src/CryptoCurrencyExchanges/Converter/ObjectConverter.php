<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Converter;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ObjectConverter implements ConverterInterface
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @param array  $data
     * @param string $className
     *
     * @throws \Kefzce\CryptoCurrencyExchanges\Converter\UnableToFindResourceException
     *
     * @return array|mixed|object|void
     */
    public function convert(array $data, string $className)
    {
        if (!class_exists($className)) {
            throw  new UnableToFindResourceException(sprintf(
                'Unable to find provided resource "%s"',
                $className
            ));
        }

        /**
         * @psalm-suppress UndefinedInterfaceMethod
         * @psalm-suppress MixedAssignment
         */
        $data = $this->serializer->denormalize($data, $className);

        return $this->verifyObjectIsOk($data);
    }

    /**
     * @param mixed $data
     *
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     *
     * @return mixed
     */
    private function verifyObjectIsOk($data)
    {
        /** @var \Symfony\Component\Validator\ConstraintViolationList $errors */
        $errors = $this->validator->validate($data);

        try {
            $this->assertValid($errors);
        } catch (ValidatorException $exception) {
            throw $exception;
        }

        return $data;
    }

    /**
     * @param \Symfony\Component\Validator\ConstraintViolationList $errors
     *
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     */
    private function assertValid(ConstraintViolationList $errors): void
    {
        if (\count($errors) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $errorsMessage = sprintf(
                    '"%s" %s',
                    $error->getPropertyPath(),
                    $error->getMessage()
                );
            }

            if (!empty($errorsMessage)) {
                throw new ValidatorException(
                    (string) $errorsMessage
                );
            }
        }
    }
}
