<?php

namespace Drupal\addtoany_snowflake\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\addtoany\Plugin\Block\AddToAnyBlock;
use Drupal\Core\Form\FormStateInterface;
use Drupal\addtoany\Form\AddToAnySettingsForm;
use Drupal\Core\Entity\ContentEntityType;

/**
 * Provides an 'AddToAny Snowflake' block.
 *
 * @Block(
 *   id = "addtoany_snowflake_block",
 *   admin_label = @Translation("AddToAny Snowflake buttons"),
 * )
 */
class AddToAnySnowflakeBlock extends AddToAnyBlock {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    if (is_numeric($node)) {
      $node = Node::load($node);
    }
    $config = \Drupal::service('config.factory')->getEditable('snowflake');
    $config->setData($this->configuration['addtoany']);
    $data = addtoany_create_entity_data($node, $config);
    return [
      '#addtoany_html'              => \Drupal::token()->replace($data['addtoany_html'], ['node' => $node]),
      '#link_url'                   => $data['link_url'],
      '#link_title'                 => $data['link_title'],
      '#button_setting'             => $data['button_setting'],
      '#button_image'               => $data['button_image'],
      '#universal_button_placement' => $data['universal_button_placement'],
      '#buttons_size'               => $data['buttons_size'],
      '#theme'                      => 'addtoany_standard',
      '#cache'                      => [
        'contexts' => ['url'],
      ],
      '#attached' => [
        'drupalSettings' => [
          'addtoanySnowflake' => [
            'css' => $config->get('additional_css'),
          ],
        ],
        'library' => [
          'addtoany_snowflake/snowflake',
        ],
      ],
    ];
  }

  /**
	* {@inheritdoc}
	*/
	public function defaultConfiguration() {
    $addtoany = \Drupal::config('addtoany.settings');
    $settings_array = $addtoany->getRawData();
    unset($settings_array['_core']);
		return array(
      'addtoany' => $settings_array,
		) + parent::defaultConfiguration();
	}

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $module_handler = \Drupal::service('module_handler');
    $addtoany_form = new AddToAnySettingsForm($module_handler);
    $form += $addtoany_form->buildForm([], $form_state);
    unset($form['actions']);

    $form['addtoany_additional_settings']['addtoany_additional_css']['#default_value'] = $this->configuration['addtoany']['additional_css'];
    $form['addtoany_button_settings']['addtoany_service_button_settings']['addtoany_additional_html']['#default_value'] = $this->configuration['addtoany']['additional_html'];
    $form['addtoany_additional_settings']['addtoany_additional_js']['#default_value'] = $this->configuration['addtoany']['additional_js'];
    $form['addtoany_button_settings']['addtoany_buttons_size']['#default_value'] = $this->configuration['addtoany']['buttons_size'];
    $form['addtoany_button_settings']['universal_button']['addtoany_custom_universal_button']['#default_value'] = $this->configuration['addtoany']['custom_universal_button'];
    $form['addtoany_button_settings']['universal_button']['addtoany_universal_button']['#default_value'] = $this->configuration['addtoany']['universal_button'];
    $form['addtoany_button_settings']['universal_button']['addtoany_universal_button_placement']['#default_value'] = $this->configuration['addtoany']['universal_button_placement'];
    $form['addtoany_additional_settings']['addtoany_no_3p']['#default_value'] = $this->configuration['addtoany']['no_3p'];

    foreach(AddToAnySettingsForm::getContentEntities() as $entity) {
      $entityId = $entity->id();
      if(isset($form['addtoany_entity_settings'][$entityId]) && isset($this->configuration['addtoany']['entities'][$entityId])){
        $form['addtoany_entity_settings'][$entityId]['#default_value'] = $this->configuration['addtoany']['entities'][$entityId];
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();

    $this->configuration['addtoany']['additional_css']  = $values['addtoany_additional_settings']['addtoany_additional_css'];
    $this->configuration['addtoany']['additional_html']  = $values['addtoany_button_settings']['addtoany_service_button_settings']['addtoany_additional_html'];
    $this->configuration['addtoany']['additional_js']  = $values['addtoany_additional_settings']['addtoany_additional_js'];
    $this->configuration['addtoany']['buttons_size']  = $values['addtoany_button_settings']['addtoany_buttons_size'];
    $this->configuration['addtoany']['custom_universal_button']  = $values['addtoany_button_settings']['universal_button']['addtoany_custom_universal_button'];
    $this->configuration['addtoany']['universal_button']  = $values['addtoany_button_settings']['universal_button']['addtoany_universal_button'];
    $this->configuration['addtoany']['universal_button_placement']  = $values['addtoany_button_settings']['universal_button']['addtoany_universal_button_placement'];
    $this->configuration['addtoany']['no_3p']  = $values['addtoany_additional_settings']['addtoany_no_3p'];

    foreach(AddToAnySettingsForm::getContentEntities() as $entity) {
      $entityId = $entity->id();
      $this->configuration['addtoany']['entities'][$entityId] = $values['addtoany_entity_settings'][$entityId];
    }
  }

}
