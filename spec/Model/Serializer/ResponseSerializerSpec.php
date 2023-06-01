<?php

declare(strict_types=1);


namespace spec\Autoprotect\DynamodbODM\Model\Serializer;

use Autoprotect\DynamodbODM\Annotation\AnnotationManager;
use Autoprotect\DynamodbODM\Annotation\AnnotationManagerInterface;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Autoprotect\DynamodbODM\Model\Serializer\ResponseSerializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Koriym\Attributes\AttributeReader;
use Koriym\Attributes\DualReader;
use PhpSpec\ObjectBehavior;
use spec\Autoprotect\DynamodbODM\Model\fixtures\EncryptionModel;

class ResponseSerializerSpec extends ObjectBehavior
{
    function let() {
        $this->beConstructedWith(new AnnotationManager(
            new DualReader(
                new AnnotationReader(),
                new AttributeReader()
            )
        ));
    }

    function it_is_initiallizable()
    {
        $this->shouldBeAnInstanceOf(ResponseSerializer::class);
    }

    public function it_should_not_encrypt_the_fields(): void
    {
        $encryptedModel = new EncryptionModel();

        $encryptedModel->setId('someId');
        $encryptedModel->setEncryptedName('secretName');

        $this->serialize($encryptedModel)
            ->offsetGet("encryptedName")
            ->shouldBe("secretName");
    }
}
