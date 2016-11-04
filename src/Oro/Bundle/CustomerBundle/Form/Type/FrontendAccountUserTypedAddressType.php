<?php

namespace Oro\Bundle\CustomerBundle\Form\Type;

use Oro\Bundle\CustomerBundle\Entity\AccountUserAddress;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FrontendAccountUserTypedAddressType extends AccountTypedAddressType
{
    const NAME = 'oro_account_frontend_account_user_typed_address';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('frontendOwner', FrontendAccountUserSelectType::NAME, [
            'label' => 'oro.customer.accountuser.entity_label'
        ]);
    }
}
