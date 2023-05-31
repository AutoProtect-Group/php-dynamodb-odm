<?php

namespace spec\Autoprotect\DynamodbODM\Annotation;

use Autoprotect\DynamodbODM\Annotation\AnnotationManagerInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use spec\Autoprotect\DynamodbODM\Annotation\fixtures\NewModel;
use spec\Autoprotect\DynamodbODM\Model\fixtures\SortKeyModel;

/**
 * Class AnnotationManagerSpec
 *
 * @package spec\DealTrak\Adapter\DynamoDBAdapter\Annotation
 */
class AnnotationManagerSpec extends AbstractAnnotationSpec
{
    function let()
    {
        include_once __DIR__ . '/../../vendor/autoload.php';
        AnnotationRegistry::registerLoader('class_exists');

        $this->beConstructedWith();
    }

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(AnnotationManagerInterface::class);
    }

    function it_gets_fields_annotaion_processed_by_class_name()
    {
        $this->getFieldTypesByModelClassName(NewModel::class)->shouldhaveTheAnnotationsLike([
            'id' => 'string',
            'name' => 'string',
            'price' => 'number',
            'isValid' => 'boolean',
        ]);
    }

    function it_gets_primary_key_by_class_name()
    {
        $this->getPrimaryKeyByModelClassName(NewModel::class)->shouldBe('id');
    }

    function it_implements_model_interface()
    {
        $this
            ->isImplementsModelInterface(NewModel::class)
            ->shouldBe(true);
    }

    function it_does_not_implements_model_interface()
    {
        $fakeClass = new class() {};

        $this
            ->isImplementsModelInterface(get_class($fakeClass))
            ->shouldBe(false);
    }

    function it_should_return_class_short_name()
    {
        $this
            ->getClassShortName(NewModel::class)
            ->shouldReturn('NewModel');
    }

    function it_should_return_sort_key_by_class_name()
    {
        $this
            ->getSortKeyByModelClassName(SortKeyModel::class)
            ->shouldBeSortKeyProperty('clientId');
    }
}
