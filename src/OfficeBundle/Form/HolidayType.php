<?php

namespace OfficeBundle\Form;

use SC\DatetimepickerBundle\Form\Type\DatetimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HolidayType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', DatetimeType::class, array(
                'pickerOptions' => array(
                    'format' => 'dd-MM-yyyy',
                    'weekstart' => 0,
                    'todayBtn' => true,
                    'todayHighlight' => true,
                    'minView' => 2,
                    'autoclose' => true,
                ),
            ))
            ->add('title', TextareaType::class, array(
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OfficeBundle\Entity\Holiday',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'officebundle_holiday';
    }
}
