<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Mapper;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class JsonMapper implements MapperInterface
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array  $data
     * @param string $className
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     *
     * @return array|mixed|object|void
     */
    public function convert(array $data, string $className)
    {
        if (!class_exists($className)) {
            return;
        }

        $objectNormalizer = new ObjectNormalizer(
            null,
            new CamelCaseToSnakeCaseNameConverter(),
            null,
            new PhpDocExtractor()
        );
        $serializer = new Serializer([$objectNormalizer], [new JsonEncoder()]);
        $data = $serializer->denormalize($data, $className);

        return $this->verifyObjectIsOk($data);
    }

    /**
     * @param object $data
     *
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     *
     * @return object|mixed
     */
    private function verifyObjectIsOk(object $data)
    {
        /** @var \Symfony\Component\Validator\ConstraintViolationList $errors */
        $errors = $this->validator->validate($data);

        if (\count($errors) > 0) {
            foreach ($errors as $error) {
                $errorsMessage = sprintf(
                    '"%s" %s',
                    $error->getPropertyPath(),
                    $error->getMessage()
                );
            }

            if (!empty($errorsMessage)) {
                throw new ValidatorException((string) $errorsMessage);
            }
        }

        return $data;
    }
}
