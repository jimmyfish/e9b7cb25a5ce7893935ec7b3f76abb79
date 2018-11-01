<?php

namespace OfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('deviceName')
        ->add('sn', TextType::class, [
            'label' => 'Serial Number',
        ])
        ->add('vc', TextType::class, [
            'label' => 'Virtual Code',
        ])
        ->add('ac', TextType::class, [
            'label' => 'Activation Code',
        ])
        ->add('vkey', TextType::class, [
            'label' => 'Virtual Key',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OfficeBundle\Entity\Device',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'officebundle_device';
    }
}
