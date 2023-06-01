<?php

namespace spec\Autoprotect\DynamodbODM\Annotation;

use Autoprotect\DynamodbODM\Annotation\Exception\DuplicatePrivatePropertyException;
use Autoprotect\DynamodbODM\Annotation\ModelAnnotationProcessor;
use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Koriym\Attributes\AttributeReader;
use Koriym\Attributes\DualReader;
use spec\Autoprotect\DynamodbODM\Annotation\fixtures\DuplicatedPrivatePropertyChildModelClass;
use spec\Autoprotect\DynamodbODM\Annotation\fixtures\ModelWithEnumeration;
use spec\Autoprotect\DynamodbODM\Annotation\fixtures\NewModel;
use spec\Autoprotect\DynamodbODM\Annotation\fixtures\NewModel8Annotations;
use spec\Autoprotect\DynamodbODM\Annotation\fixtures\PrivatePropertyChildModelClass;
use spec\Autoprotect\DynamodbODM\Model\fixtures\SortKeyModel;

/**
 * Class ModelAnnotationProcessorSpec
 *
 * @package spec\Autoprotect\DynamodbODM\Annotation
 */
class ModelAnnotationProcessorSpec extends AbstractAnnotationSpec
{
    function let(
        PropertyInterface $sortKeyProperty,
        PropertyInterface $idKeyProperty,
    )
    {
        include_once __DIR__ . "/../../vendor/autoload.php";
        AnnotationRegistry::registerLoader('class_exists');

        $sortKeyProperty->isSortKey()->willReturn(true);
        $sortKeyProperty->isPrimary()->willReturn(false);

        $idKeyProperty->isPrimary()->willReturn(true);
        $sortKeyProperty->isSortKey()->willReturn(false);


        $this->beConstructedWith(NewModel::class);
    }

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(ModelAnnotationProcessor::class);
    }

    function it_should_return_the_field_list_their_types(
        PropertyInterface $idKeyProperty,
    )
    {
        $this->getFieldTypes()->shouldHaveTheAnnotationsLike(
            [
                'id' => 'string',
                'name' => 'string',
                'price' => 'number',
                'isValid' => 'boolean',
            ]
        );
    }

    /**
     * @return void
     */
    public function it_should_throw_an_exception_when_there_is_duplicated_private_property_name(): void
    {
        $this->beConstructedWith(DuplicatedPrivatePropertyChildModelClass::class, new DualReader(
                new AnnotationReader(),
                new AttributeReader())
        );

        $this->shouldThrow(DuplicatePrivatePropertyException::class)->duringInstantiation();
    }

    /**
     * @return void
     */
    public function it_should_not_throw_an_exception_when_there_is_no_duplicated_private_property_name(): void
    {
        $this->beConstructedWith(PrivatePropertyChildModelClass::class, new DualReader(
                new AnnotationReader(),
                new AttributeReader())
        );

        $this->getFieldTypes()->shouldHaveTheAnnotationsLike(
            [
                'providerName' => 'string',
                'policyId' => 'string',
                'dealId' => 'string',
                'category' => 'string',
                'reason' => 'string',
                'type' => 'string'
            ]
        );
    }

    function it_should_return_primary_key_name()
    {
        $this
            ->getIdProperty()
            ->shouldBeIdProperty('id');
    }

    function it_should_implements_model_interface()
    {
        $this
            ->isImplementsModelInterface()
            ->shouldReturn(true);
    }

    function it_should_return_class_short_name()
    {
        $this
            ->getClassShortName()
            ->shouldReturn('NewModel');
    }

    function it_should_return_sort_key_name()
    {
        $this->beConstructedWith(SortKeyModel::class);

        $this
            ->getSortKeyProperty()
            ->shouldBeSortKeyProperty('clientId');
    }

    function it_should_process_php8_annotations()
    {
        $this->beConstructedWith(NewModel8Annotations::class, new DualReader(
            new AnnotationReader(),
            new AttributeReader())
        );

        $this
            ->getClassShortName()
            ->shouldReturn('NewModel8Annotations');

        $this
            ->getSortKeyProperty()
            ->shouldBeSortKeyProperty('php8SortKey');

        $this
            ->getIdProperty()
            ->shouldBeIdProperty('idPhp8Attribute');

        $this
            ->getFieldTypes()
            ->shouldHaveTheAnnotationsLike([
                'idPhp8Attribute'      => 'string',
                'php8SortKey'    => 'string',
                'intProperty'   => 'integer',
                'boolProperty' => 'boolean',
                'dateTimeProperty' => 'datetime',
                'injectedModel' => 'model',
                'inlineConstructorProperty' => 'integer',
            ]);

        $this->getAnnotationModelClassNameByFieldName('injectedModel')->shouldReturn(NewModel::class);
    }

    function it_should_process_enumeration_type()
    {
        $this->beConstructedWith(ModelWithEnumeration::class, new AttributeReader());

        $this
            ->getClassShortName()
            ->shouldReturn('ModelWithEnumeration');

        $this
            ->getIdProperty()
            ->shouldBeIdProperty('id');

        $this
            ->getFieldTypes()
            ->shouldHaveTheAnnotationsLike([
                'id'      => 'string',
                'backedEnumerationStrict'    => 'enum',
                'backedEnumerationNotStrict'   => 'enum',
                'unitEnumeration' => 'enum',
            ]);
    }
}
