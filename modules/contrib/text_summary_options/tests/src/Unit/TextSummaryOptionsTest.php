<?php

declare(strict_types=1);

namespace Drupal\Tests\text_summary_options\Unit;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Form\FormState;
use Drupal\field\Entity\FieldConfig;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;

include_once __DIR__ . '/../../../text_summary_options.module';

/**
 * Tests text_summary_options alter hook.
 *
 * @group text_summary_options
 */
class TextSummaryOptionsTest extends UnitTestCase {

  /**
   * Tests hook_field_widget_single_element_form_alter().
   */
  public function testFieldWidgetSingleElementFormAlterHook(): void {
    $element = [
      'summary' => [
        '#attached' => [],
      ],
    ];

    $fieldConfig = $this->prophesize(FieldConfig::class);
    $fieldConfig->getThirdPartySetting('text_summary_options', 'show_summary')
      ->willReturn(1);
    $fieldConfig->getThirdPartySetting('text_summary_options', 'summary_help')
      ->willReturn('Foo bar baz');
    $fieldConfig->getThirdPartySetting('text_summary_options', 'summary_placeholder')
      ->willReturn('Whiz bang');

    $fieldDefinition = $this->prophesize(FieldConfig::class);
    $fieldDefinition->getType()->willReturn('text_with_summary');
    $fieldDefinition->getTargetBundle()->willReturn(NULL);
    $fieldDefinition->getConfig(Argument::any())->willReturn($fieldConfig->reveal());

    $fieldItem = $this->prophesize(FieldItemInterface::class);
    $fieldItem->getFieldDefinition()->willReturn($fieldDefinition->reveal());
    $context = ['items' => $fieldItem->reveal()];

    $formState = new FormState();
    $formState->set('default_value_widget', FALSE);
    \text_summary_options_field_widget_single_element_form_alter($element, $formState, $context);

    $expected = [
      'summary' => [
        '#description' => 'Foo bar baz',
        '#attributes' => [
          'placeholder' => ['Whiz bang'],
        ],
      ],
    ];

    $this->assertEquals($expected, $element);
  }

}
