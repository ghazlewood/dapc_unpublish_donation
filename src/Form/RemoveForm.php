<?php

namespace Drupal\unpublish_donation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * Class DefaultForm.
 */
class RemoveForm extends FormBase
{


  /**
   * {@inheritdoc}
   */
    public function getFormId()
    {
        return 'unpublish_donation_remove_form';
    }

  /**
   * {@inheritdoc}
   */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $node = \Drupal::routeMatch()->getParameter('node');
        if ($node instanceof \Drupal\node\NodeInterface) {
            $nid = $node->id();
        }

        $form['maiden_name'] = array(
          '#type' => 'password',
          '#title' => $this->t('Your Mother\'s Maiden Name')
        );
        $form['nid'] = array(
          '#type' => 'hidden',
          '#value' => $nid,
        );
        $form['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Remove Donation'),
        ];

        return $form;
    }

  /**
   * {@inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
      // Load the node value from the field
        $nid = $form_state->getValue('nid');
        $node = node_load((int) $nid);
        $original_maiden_name = $node->field_your_mothers_maiden_name->value;
        $submitted_maiden_name = $form_state->getValue('maiden_name');

      /*if (empty($form_state->getValue('maiden_name'))) {
         $form_state->setErrorByName('maiden_name', $this->('Please enter a Maiden Name'));
      }*/

      // If the fields don't match throw an error on the field
        if ($submitted_maiden_name != $original_maiden_name) {
          // TO DO change to
          // Sorry: this has not matched your original entry.
          // Please contact us and we will remove your donation.
          // Or try again:
          // ** needs link to contact us
            $form_state->setErrorByName('maiden_name', $this->t('Sorry you have not entered the correct Maiden Name, please try again.'));
        }
    }

  /**
   * {@inheritdoc}
   */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
      // Unpublish Node
        $nid = $form_state->getValue('nid');
        $node = \Drupal\node\Entity\Node::load($nid);
        $node->setPublished(false);
        $node->save();

        drupal_set_message('Your donation has been removed.');
        $url = \Drupal\Core\Url::fromRoute('view.donations.page_donations');
        $form_state->setRedirectUrl($url);
    }
}
